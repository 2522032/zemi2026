from flask import Flask, request, jsonify
from PIL import Image
import numpy as np
import tensorflow as tf
import os

app = Flask(__name__)

# モデル読み込み（Keras互換エラー回避）
model = tf.keras.models.load_model(
    "model/mahjong_model.h5",
    compile=False
)

# ラベル
categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

@app.route("/")
def home():
    return "API OK"

@app.route("/predict", methods=["POST"])
def predict():
    # 画像取得
    if "image" not in request.files:
        return jsonify({"error": "no image provided"}), 400

    file = request.files["image"]

    # 前処理
    img = Image.open(file).convert("RGB").resize((150, 150))
    data = np.array(img, dtype=np.float32) / 255.0
    data = np.expand_dims(data, axis=0)

    # 予測
    pred = model.predict(data, verbose=0)
    idx = int(np.argmax(pred))

    return jsonify({
        "result": categories[idx],
        "confidence": float(np.max(pred))
    })

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    app.run(host="0.0.0.0", port=port)