from flask import Flask, request, jsonify
from PIL import Image
import numpy as np
import tensorflow as tf
import os
import traceback

app = Flask(__name__)

# =========================
# モデルロード（安全）
# =========================
model = None

def load_model_safe():
    global model
    try:
        model = tf.keras.models.load_model(
            "model/mahjong_model.h5",
            compile=False
        )
        print("✅ MODEL LOAD SUCCESS")
    except Exception as e:
        print("❌ MODEL LOAD ERROR")
        print(traceback.format_exc())
        model = None

load_model_safe()

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
# 予測API
# =========================
@app.route("/predict", methods=["POST"])
def predict():
    try:
        print("➡ REQUEST RECEIVED")

        # 画像チェック
        if "image" not in request.files:
            return jsonify({
                "error": "NO_IMAGE",
                "detail": "imageキーが送られていない"
            }), 400

        file = request.files["image"]

        # 画像処理
        img = Image.open(file).convert("RGB").resize((150, 150))
        data = np.array(img, dtype=np.float32) / 255.0
        data = np.expand_dims(data, axis=0)

        print("➡ IMAGE OK")

        # モデルチェック
        if model is None:
            return jsonify({
                "error": "MODEL_NOT_LOADED"
            }), 500

        # 推論
        pred = model.predict(data, verbose=0)

        idx = int(np.argmax(pred))
        confidence = float(np.max(pred))

        print("➡ PRED DONE:", idx)

        return jsonify({
            "result": categories[idx],
            "confidence": confidence
        })

    except Exception as e:
        # 🔥 ここが最重要（エラー全部見える）
        print("🔥 INTERNAL ERROR")
        print(traceback.format_exc())

        return jsonify({
            "error": "INTERNAL_ERROR",
            "message": str(e),
            "trace": traceback.format_exc()
        }), 500


# =========================
# Render起動
# =========================
if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host="0.0.0.0", port=port)