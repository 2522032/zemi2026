from keras import layers, models
from keras.utils import to_categorical
import numpy as np

categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

def build_model():
    model = models.Sequential([
        layers.Conv2D(32, (3,3), activation="relu", input_shape=(150,150,3)),
        layers.MaxPooling2D(2,2),

        layers.Conv2D(64, (3,3), activation="relu"),
        layers.MaxPooling2D(2,2),

        layers.Conv2D(128, (3,3), activation="relu"),
        layers.MaxPooling2D(2,2),

        layers.Conv2D(128, (3,3), activation="relu"),
        layers.MaxPooling2D(2,2),

        layers.Flatten(),
        layers.Dense(512, activation="relu"),
        layers.Dense(len(categories), activation="softmax")
    ])

    model.compile(
        optimizer="adam",
        loss="categorical_crossentropy",
        metrics=["accuracy"]
    )

    return model


if __name__ == "__main__":
    model = build_model()

    # 仮データ（ここは自分のデータに置き換え）
    X = np.random.random((100,150,150,3))
    y = to_categorical(np.random.randint(0, len(categories), 100), num_classes=len(categories))

    model.fit(X, y, epochs=3)

    # ★重要：keras形式で保存
    model.save("model/mahjong_model.keras")
    print("saved model")