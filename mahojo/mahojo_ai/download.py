from bing_image_downloader import downloader

classes = {
    "1m": "mahjong 1m tile",
    "1p": "mahjong 1p tile",
    "1s": "mahjong 1s tile",
}

for folder, keyword in classes.items():
    downloader.download(
        keyword,
        limit=5,
        output_dir="data/images",
        adult_filter_off=True,
        force_replace=False,
        timeout=60,
        verbose=True
    )