from flask import Flask, request, jsonify
from PIL import Image
import numpy as np
import tensorflow as tf
import os
import traceback

app = Flask(__name__)

# ===== パスは絶対化（重要）=====
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "model", "mahjong_model.h5")

model = None

# ===== デバッグ表示付きモデルロード =====
def load_model_safe():
    global model

    try:
        print("\n===== MODEL LOAD DEBUG =====")
        print("BASE_DIR:", BASE_DIR)
        print("MODEL_PATH:", MODEL_PATH)
        print("MODEL EXISTS:", os.path.exists(MODEL_PATH))
        print("FILES IN BASE:", os.listdir(BASE_DIR))

        model = tf.keras.models.load_model(
            MODEL_PATH,
            compile=False
        )

        print("MODEL LOADED SUCCESS\n")

    except Exception as e:
        print("\nMODEL LOAD ERROR")
        print(e)
        traceback.print_exc()
        model = None


load_model_safe()

# ===== 麻雀ラベル =====
categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

# ===== ヘルスチェック =====
@app.route("/")
def home():
    return jsonify({
        "status": "API OK",
        "model_loaded": model is not None
    })

# ===== 予測API =====
@app.route("/predict", methods=["POST"])
def predict():

    # モデル未ロード
    if model is None:
        return jsonify({
            "error": "MODEL_NOT_LOADED"
        }), 500

    # 画像なし
    if "image" not in request.files:
        return jsonify({
            "error": "NO_IMAGE"
        }), 400

    try:
        file = request.files["image"]

        # 前処理
        img = Image.open(file).convert("RGB").resize((150, 150))
        data = np.array(img, dtype=np.float32) / 255.0
        data = np.expand_dims(data, axis=0)

        # 推論
        pred = model.predict(data, verbose=0)
        idx = int(np.argmax(pred))

        return jsonify({
            "result": categories[idx],
            "confidence": float(np.max(pred))
        })

    except Exception as e:
        print("INFERENCE ERROR:", e)
        traceback.print_exc()

        return jsonify({
            "error": "INFERENCE_ERROR",
            "detail": str(e)
        }), 500


# ===== Render対応起動 =====
if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host="0.0.0.0", port=port)