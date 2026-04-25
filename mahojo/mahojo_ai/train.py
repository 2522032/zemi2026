import numpy as np
from keras.utils import to_categorical
from mahojo.mahojo_ai.model import build_model, categories

data = np.load("data/tea_data.npy", allow_pickle=True)
X_train, X_test, y_train, y_test = data

# 正規化
X_train = X_train.astype("float32") / 255
X_test = X_test.astype("float32") / 255

# one-hot
y_train = to_categorical(y_train, len(categories))
y_test = to_categorical(y_test, len(categories))

# モデル作成
model = build_model()

# 学習
model.fit(
    X_train, y_train,
    epochs=10,
    batch_size=6,
    validation_data=(X_test, y_test)
)

# 保存
model.save("model/mahjong_model.h5")
print(X_train.shape)