<p align="center">
  <img src="icon.png" alt="AutoAnnouncer Logo" width="200">
</p>

<h1 align="center">AutoAnnouncer Plugin</h1>

<p align="center">
  <strong>An automated announcement plugin for your Pocketmine-MP server</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#configuration">Configuration</a> •
  <a href="#commands">Commands</a> •
</p>

---

### Features

- Automatically broadcasts customizable messages at regular intervals to keep your players engaged.
- Supports message prefixes to add a distinct identity to your announcements.
- Configurable interval between each message, allowing you to fine-tune the frequency.
- Easy-to-use configuration file for managing messages and settings.

### Installation

1. Download the latest plugin release from the [Releases](https://github.com/iLVOEWOCK/AutoAnnouncer/releases) page.
2. Place the downloaded `AutoAnnouncer.phar` file into your server's `plugins` directory.
3. Restart your server.
4. Customize the plugin's settings and messages in the `config.yml` file.

### Configuration

The plugin configuration file `config.yml` allows you to customize various aspects of the AutoAnnouncer plugin:

```yaml
# Configuration for AutoAnnouncer plugin

# The prefix message
# Use '&' for colors
prefix: "&r&8[&aAA&8] "

# Enable/disable the use of prefixes.
# true = on, false = off
use-prefix: true

# Interval between each message in ticks (20 ticks per second) (Default: 1200 ticks = 1 minute)
interval: 1200

# List of messages to be broadcasted
# Use '&' for colors, Use `\n` to break the line
messages:
  - "&r&aWelcome to the server! \n&r&7Read /rules!"
  - "&r&7Enjoy your stay!"
  - "&r&6Visit our website at example.com"
