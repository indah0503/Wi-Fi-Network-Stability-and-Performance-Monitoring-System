from flask import Flask, send_file
import heatmap_generator
import io

app = Flask(__name__)

@app.route("/heatmap")
def get_heatmap():
    path = heatmap_generator.generate_heatmap_image()
    return send_file(path, mimetype='image/png')

if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
