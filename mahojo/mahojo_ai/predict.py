import numpy as np
from PIL import Image
from mahojo.mahojo_ai.model import load_model, categories

model = load_model()

def predict_image(path):
    img = Image.open(path).convert("RGB").resize((150,150))
    data = np.array(img).astype("float32") / 255
    data = np.expand_dims(data, axis=0)

    pred = model.predict(data)
    idx = np.argmax(pred)

    return categories[idx], float(np.max(pred))


if __name__ == "__main__":
    result, conf = predict_image("test.jpg")
    print(result, conf)