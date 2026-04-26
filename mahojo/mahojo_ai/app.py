import os
import traceback
from flask import Flask, request, jsonify
from PIL import Image
import numpy as np
import tensorflow as tf

app = Flask(__name__)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# ⭐ ここ重要：両対応にする（h5 / kerasどっちでもOKにする）
MODEL_PATH_H5 = os.path.join(BASE_DIR, "model", "mahjong_model.h5")
MODEL_PATH_KERAS = os.path.join(BASE_DIR, "model", "mahjong_model.keras")

model = None

categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

def load_model_safe():
    global model

    try:
        print("=== MODEL DEBUG ===")
        print("BASE_DIR:", BASE_DIR)
        print("H5 EXISTS:", os.path.exists(MODEL_PATH_H5))
        print("KERAS EXISTS:", os.path.exists(MODEL_PATH_KERAS))

        path = None
        if os.path.exists(MODEL_PATH_KERAS):
            path = MODEL_PATH_KERAS
        elif os.path.exists(MODEL_PATH_H5):
            path = MODEL_PATH_H5
        else:
            raise FileNotFoundError("NO MODEL FILE FOUND")

        model = tf.keras.models.load_model(path, compile=False)
        print("MODEL LOADED:", path)

    except Exception as e:
        print("MODEL LOAD FAILED:", e)
        traceback.print_exc()
        model = None

load_model_safe()

@app.route("/")
def home():
    return jsonify({
        "status": "OK",
        "model_loaded": model is not None
    })

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

        pred = model.predict(data, verbose=0)
        idx = int(np.argmax(pred))

        return jsonify({
            "result": categories[idx],
            "confidence": float(np.max(pred))
        })

    except Exception as e:
        return jsonify({
            "error": "INFERENCE_ERROR",
            "detail": str(e)
        }), 500


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host="0.0.0.0", port=port)