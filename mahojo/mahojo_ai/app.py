from flask import Flask, request, jsonify
from PIL import Image
import numpy as np
import tensorflow as tf

app = Flask(__name__)

model = tf.keras.models.load_model("model/mahjong_model.h5")

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
    file = request.files["image"]

    img = Image.open(file).convert("RGB").resize((150,150))
    data = np.array(img)/255.0
    data = np.expand_dims(data, axis=0)

    pred = model.predict(data)
    idx = np.argmax(pred)

    return jsonify({
        "result": categories[idx],
        "confidence": float(np.max(pred))
    })

# Render用
if __name__ == "__main__":
    app.run(host="0.0.0.0", port=10000)