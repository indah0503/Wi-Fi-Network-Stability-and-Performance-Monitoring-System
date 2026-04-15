from flask import Flask, send_file
import cv2
import mysql.connector
import seaborn as sns
import matplotlib.pyplot as plt
from matplotlib.patches import Circle
from matplotlib import colors as mcolors  
from dotenv import load_dotenv
import os

load_dotenv()

scale = 8.62

fixed_coords = {
    "data_esp32_1": (564, 307),
    "data_esp32_2": (318, 306),
    "data_esp32_3": (390, 221),
    "data_esp32_4": (544, 134),
    "data_esp32_5": (367, 136),
}

def get_color_by_rssi(rssi, min_rssi=-100, mid_rssi=-59, max_rssi=-18):
    rssi = max(min_rssi, min(rssi, max_rssi))
    if rssi <= mid_rssi:
        ratio = (rssi - min_rssi) / (mid_rssi - min_rssi)
        red = 255
        green = int(255 * ratio)
    else:
        ratio = (rssi - mid_rssi) / (max_rssi - mid_rssi)
        red = int(255 * (1 - ratio))
        green = 255
    blue = 0
    return '#{:02x}{:02x}{:02x}'.format(red, green, blue)

def get_latest_data(table_name):
    conn = mysql.connector.connect(
        host=os.getenv("DB_HOST"),
        user=os.getenv("DB_USER"),
        password=os.getenv("DB_PASS"),
        database=os.getenv("DB_NAME")
    )
    cursor = conn.cursor(dictionary=True)
    cursor.execute(f"SELECT location, strength, distance FROM {table_name} ORDER BY time DESC LIMIT 1")
    result = cursor.fetchone()
    cursor.close()
    conn.close()
    return result

def generate_heatmap_image():
    denah = cv2.imread("assets/img/lantai1.jpeg")
    if denah is None:
        return None

    fig, ax = plt.subplots(figsize=(12, 8))
    ax.imshow(cv2.cvtColor(denah, cv2.COLOR_BGR2RGB))

    for table, (x, y) in fixed_coords.items():
        data = get_latest_data(table)
        if data:
            rssi = data["strength"]
            distance = float(data["distance"])
            location = data["location"]
            diameter_px = distance * scale
            radius_px = diameter_px / 2
            center_x = x + radius_px
            center_y = y
            color = get_color_by_rssi(rssi)
            
            circle = Circle((center_x, center_y), radius_px, color=color, alpha=0.9)
            ax.add_patch(circle)
            ax.plot(x, y, 'ko')
            ax.text(x, y, location, fontsize=6, color='black', ha='center', va='center',
                    bbox=dict(boxstyle="round,pad=0.2", fc="white", ec="black", lw=0.5))
            ax.text(x, y + 12, f"{rssi} dBm", fontsize=6, color='black', ha='center', va='top')

    cmap = mcolors.LinearSegmentedColormap.from_list(
        'rssi_gradient', ['#ff0000', '#ffff00', '#00ff00']
    )
    norm = mcolors.Normalize(vmin=-100, vmax=-18)
    sm = plt.cm.ScalarMappable(norm=norm, cmap=cmap)
    sm.set_array([])
    cbar = plt.colorbar(sm, ax=ax, orientation='horizontal', pad=0.05, aspect=50)
    cbar.set_label("Kekuatan Sinyal (dBm)")
    cbar.set_ticks([-100, -59, -18])

    plt.subplots_adjust(bottom=0.15)
    ax.axis('off')

    output_path = "mnt/data/heatmap.png"
    plt.savefig(output_path, dpi=300)
    plt.close()
    return output_path
