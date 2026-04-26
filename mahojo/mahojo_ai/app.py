import os
import numpy as np
import traceback
from flask import Flask, request, jsonify
from PIL import Image
import tensorflow as tf

app = Flask(__name__)

print("=== FILE TREE ===")
for root, dirs, files in os.walk("."):
    print(root)
    for f in files:
        print(" -", f)
# =========================
# パス設定
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "model", "mahjong_model.keras")

# =========================
# ラベル
# =========================
categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

model = None

# =========================
# モデルロード
# =========================
def load_model():
    global model

    try:
        print("=== MODEL DEBUG ===")
        print("BASE_DIR:", BASE_DIR)
        print("MODEL_PATH:", MODEL_PATH)
        print("EXISTS:", os.path.exists(MODEL_PATH))

        if not os.path.exists(MODEL_PATH):
            raise FileNotFoundError("MODEL FILE NOT FOUND")

        model = tf.keras.models.load_model(MODEL_PATH, compile=False)
        print("MODEL LOADED OK")

        # 🔥ウォームアップ（遅延＆Gateway Timeout対策）
        dummy = np.zeros((1, 150, 150, 3), dtype=np.float32)
        model.predict(dummy, verbose=0)
        print("WARMUP DONE")

    except Exception as e:
        print("MODEL LOAD FAILED:", e)
        traceback.print_exc()
        model = None


load_model()

# =========================
# ヘルスチェック
# =========================
@app.route("/")
def home():
    return jsonify({
        "status": "OK",
        "model_loaded": model is not None
    })

# =========================
# 推論API
# =========================
@app.route("/predict", methods=["POST"])
def predict():

    if model is None:
        return jsonify({"error": "MODEL_NOT_LOADED"}), 500

    if "image" not in request.files:
        return jsonify({"error": "NO_IMAGE"}), 400

    try:
        file = request.files["image"]

        img = Image.open(file).convert("RGB").resize((150, 150))
        data = np.array(img, dtype=np.float32) / 255.0
        data = np.expand_dims(data, axis=0)

        # 🔥軽量＆安定推論
        pred = model.predict(data, verbose=0, batch_size=1)

        idx = int(np.argmax(pred))
        confidence = float(np.max(pred))

        return jsonify({
            "result": categories[idx],
            "confidence": confidence
        })

    except Exception as e:
        return jsonify({
            "error": "INFERENCE_ERROR",
            "detail": str(e)
        }), 500


# =========================
# 起動
# =========================
if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host="0.0.0.0", port=port)