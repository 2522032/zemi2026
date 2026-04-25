from PIL import Image
import os, glob
import numpy as np
import random

os.makedirs("data", exist_ok=True)

root_dir = "data/images"

categories = [
    "1m","2m","3m","4m","5m","6m","7m","8m","9m",
    "1p","2p","3p","4p","5p","6p","7p","8p","9p",
    "1s","2s","3s","4s","5s","6s","7s","8s","9s",
    "east","south","west","north",
    "white","green","red"
]

def load_image(path):
    img = Image.open(path).convert("RGB").resize((150,150))
    return np.asarray(img)

allfiles = []

for idx, cat in enumerate(categories):
    image_dir = f"{root_dir}/{cat}"
    files = glob.glob(image_dir + "/*.jpg")

    if len(files) == 0:
        print(f"SKIP empty folder: {cat}")
        continue

    for f in files:
        allfiles.append((idx, f))

random.shuffle(allfiles)

th = int(len(allfiles) * 0.8)
train, test = allfiles[:th], allfiles[th:]

def make_dataset(data):
    X, Y = [], []
    for label, fname in data:
        X.append(load_image(fname))
        Y.append(label)
    return np.array(X), np.array(Y)

X_train, y_train = make_dataset(train)
X_test, y_test = make_dataset(test)

np.save("data/tea_data.npy", np.array([X_train, X_test, y_train, y_test], dtype=object))

print("dataset saved")
print(X_train.shape, y_train.shape)