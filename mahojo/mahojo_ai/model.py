from keras import layers, models

categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

def build_model():
    model = models.Sequential([
        layers.Conv2D(32,(3,3),activation="relu",input_shape=(150,150,3)),
        layers.MaxPooling2D(2,2),

        layers.Conv2D(64,(3,3),activation="relu"),
        layers.MaxPooling2D(2,2),

        layers.Conv2D(128,(3,3),activation="relu"),
        layers.MaxPooling2D(2,2),

        layers.Conv2D(128,(3,3),activation="relu"),
        layers.MaxPooling2D(2,2),

        layers.Flatten(),
        layers.Dense(512,activation="relu"),
        layers.Dense(len(categories),activation="softmax")
    ])

    model.compile(
        loss="categorical_crossentropy",
        optimizer="adam",
        metrics=["accuracy"]
    )

    return model