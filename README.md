# Quran Player Moodle Block Plugin 🎧🕌

The **Quran Player Block Plugin** is a Moodle block designed to enhance the learning experience by integrating an audio player for Quran chapters directly into Moodle courses. This plugin allows users to play Quran audio files and display the corresponding Quran text alongside the audio.

## Features ✨
- **Audio Player**: Play Quran chapters directly in Moodle.
- **Quran Text Display**: Display the corresponding Quran text for the selected chapter.
- **Easy Integration**: Add the block to any Moodle course or dashboard.
- **Customizable**: Easily customize the player's appearance using CSS.
- **Error Handling**: Gracefully handles missing files or directories.

---

## Installation 🛠️

### Prerequisites
- Moodle 3.11 or later.
- PHP 7.4 or later.
- Access to the Moodle `blocks` directory.

### Steps
1. **Download the Plugin**:
   - Clone this repository or download the ZIP file.
   ```bash
   git clone https://github.com/maysaraadmin/moodle-block_quranplayer.git
   ```

2. **Install the Plugin**:
   - Copy the `quranplayer` folder to your Moodle `blocks` directory:
     ```
     moodle/blocks/quranplayer
     ```

3. **Install via Moodle**:
   - Log in to your Moodle site as an administrator.
   - Navigate to **Site Administration > Notifications**.
   - Moodle will detect the new block and install it automatically.

4. **Add MP3 Files**:
   - Place your Quran audio files (in MP3 format) in the `mp3` directory:
     ```
     moodle/blocks/quranplayer/mp3/
     ```
   - Ensure the files are named sequentially (e.g., `001.mp3`, `002.mp3`, etc.).

5. **Add Quran Text**:
   - Place the `quran.txt` file in the plugin directory:
     ```
     moodle/blocks/quranplayer/quran.txt
     ```
   - The file should contain Quran text in the format:
     ```
     surah|verse|text
     ```
     Example:
     ```
     1|1|بسم الله الرحمن الرحيم
     ```

---

## Usage 🎯

1. **Add the Block**:
   - Go to the course or dashboard where you want to add the block.
   - Turn editing on and click **"Add a block"**.
   - Select **"Quran Player"** from the list of blocks.

2. **Select a Chapter**:
   - Use the dropdown menu to select a Quran chapter.
   - The audio player will load the selected chapter, and the corresponding Quran text will be displayed.

3. **Customize the Player**:
   - You can customize the appearance of the player by editing the `styles.css` file in the plugin directory.

---
## Contributing 🤝

We welcome contributions from the community! Whether you're a developer, tester, or documentation enthusiast, your help is appreciated.

### How to Contribute
1. **Fork the Repository**:
   - Fork this repository to your GitHub account.

2. **Clone the Repository**:
   ```bash
   git clone https://github.com/maysaraadmin/moodle-block_quranplayer.git
   ```

3. **Create a Branch**:
   - Create a new branch for your feature or bug fix:
     ```bash
     git checkout -b feature/your-feature-name
     ```

4. **Make Changes**:
   - Make your changes and test them thoroughly.

5. **Submit a Pull Request**:
   - Push your changes to your forked repository and submit a pull request to the `main` branch of this repository.

### Contribution Guidelines
- Follow Moodle's [coding standards](https://docs.moodle.org/dev/Coding_style).
- Write clear commit messages and include comments in your code.
- Test your changes before submitting a pull request.

---

## License 📜

This plugin is released under the **GNU General Public License v3.0**. See the [LICENSE](LICENSE) file for details.

---

## Support and Feedback 💬

If you encounter any issues or have suggestions for improvement, please:
- Open an issue on [GitHub](https://github.com/maysaraadmin/moodle-block_quranplayer/issues).
- Reach out to me via email or LinkedIn.

Let’s make this plugin even better together! 🌟

---

## Acknowledgments 🙏

- Thanks to the Moodle community for their support and resources.
- Special thanks to all contributors who help improve this plugin.

---

🔗 **GitHub Repository**: [https://github.com/maysaraadmin/moodle-block_quranplayer](https://github.com/maysaraadmin/moodle-block_quranplayer)