from flask import Flask, request, jsonify
from PIL import Image
import numpy as np
import tensorflow as tf
import os

app = Flask(__name__)

<<<<<<< HEAD
model = None

def get_model():
    global model
    if model is None:
        model = tf.keras.models.load_model(
            "model/mahjong_model.h5",
            compile=False
        )
    return model

=======
# =========================
# モデルロード（安定化）
# =========================
def load_model_safe():
    return tf.keras.models.load_model(
        "model/mahjong_model.h5",
        compile=False
    )

model = load_model_safe()

# =========================
# ラベル定義
# =========================
>>>>>>> cad81f86 (fix_app.py)
categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

# =========================
# ヘルスチェック
# =========================
@app.route("/")
def home():
    return "API OK"

# =========================
# 牌予測API
# =========================
@app.route("/predict", methods=["POST"])
def predict():
<<<<<<< HEAD
    if "image" not in request.files:
        return jsonify({"error": "no image"}), 400
=======
    try:
        # 画像チェック
        if "image" not in request.files:
            return jsonify({"error": "no image provided"}), 400
>>>>>>> cad81f86 (fix_app.py)

        file = request.files["image"]

<<<<<<< HEAD
    img = Image.open(file).convert("RGB").resize((150, 150))
    data = np.array(img, dtype=np.float32) / 255.0
    data = np.expand_dims(data, axis=0)

    pred = get_model().predict(data, verbose=0)
    idx = int(np.argmax(pred))
=======
        # 画像前処理
        img = Image.open(file).convert("RGB").resize((150, 150))
        data = np.array(img, dtype=np.float32) / 255.0
        data = np.expand_dims(data, axis=0)

        # 推論
        pred = model.predict(data, verbose=0)
>>>>>>> cad81f86 (fix_app.py)

        idx = int(np.argmax(pred))
        confidence = float(np.max(pred))

        return jsonify({
            "result": categories[idx],
            "confidence": confidence
        })

    except Exception as e:
        return jsonify({
            "error": str(e)
        }), 500


# =========================
# Render用起動設定
# =========================
if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host="0.0.0.0", port=port)