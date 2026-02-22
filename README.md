# Virtualcode Click to Chat

A lightweight WordPress plugin that adds a floating WhatsApp chat button to your website. Let visitors start a conversation with you instantly with just one click!

[![WordPress](https://img.shields.io/badge/WordPress-5.9%2B-blue)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple)](https://php.net)
[![License](https://img.shields.io/badge/license-GPLv2-green)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.0.0-orange)](https://github.com/yourusername/virtualcode-click-to-chat)

## 📋 Description

Virtualcode Click to Chat helps you connect with your website visitors instantly via WhatsApp. With a fully customizable floating chat button, your users can start a WhatsApp conversation with you in just one click.

This plugin is designed to be lightweight, easy to configure, and highly customizable. You can control where and when the WhatsApp button appears, customize its style, and manage visibility based on pages, devices, and business hours.

Perfect for businesses, freelancers, agencies, and eCommerce stores who want faster communication and higher conversions.

## ✨ Key Features

### 🎯 Core Features
- **Floating WhatsApp Chat Button** - Always accessible, never intrusive
- **Prefilled Message Support** - Set default messages for visitors
- **Device Targeting** - Show on Desktop, Mobile, or both
- **Page Targeting** - Include/exclude specific pages
- **Business Hours** - Show only during working hours
- **Delay Popup** - Appear after X seconds

### 🎨 Customization Options
- **Position** - Left or right side
- **Gap Control** - Bottom and side margins
- **Colors** - Custom background and text colors
- **Sizes** - Adjustable icon and text sizes
- **Icon Only Mode** - Show just the icon or icon + text
- **Animations** - Smooth entrance and hover effects

### ⚡ Performance
- **Lightweight** - Minimal impact on page load
- **Optimized CSS/JS** - Only loads when needed
- **No External APIs** - Works with WhatsApp click-to-chat links
- **Mobile Responsive** - Works perfectly on all devices

## 📸 Screenshots

1. **Floating Button** - WhatsApp chat button on the frontend
2. **General Settings** - Basic configuration panel
3. **Appearance Settings** - Style customization options
4. **Advanced Settings** - Page targeting & business hours

## 🚀 Installation

### Via WordPress Admin
1. Go to Plugins → Add New
2. Search for "Virtualcode Click to Chat"
3. Click Install Now → Activate

### Manual Installation
1. Upload the `virtualcode-click-to-chat` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Click to Chat** in the WordPress admin menu
4. Configure your settings and save

## ⚙️ Configuration

### General Settings
- **Enable/Disable** - Toggle the chat button on/off
- **WhatsApp Number** - Your number with country code
- **Prefilled Message** - Default message for visitors
- **Device Display** - Choose where to show the button

### Appearance Settings
- **Position** - Left or right side
- **Gap** - Distance from screen edges
- **Colors** - Background and text colors
- **Sizes** - Icon and text size
- **Display Mode** - Icon only or icon + text

### Advanced Settings
- **Page Targeting** - Show on specific pages only
- **Delay** - Show after X seconds
- **Business Hours** - Set days and times for visibility

## 📱 Device Detection

The plugin includes a lightweight mobile detection class that automatically identifies:
- Desktop devices
- Mobile phones
- Tablets

You can choose to show the button on all devices or target specific device types.

## 🕒 Business Hours

Set specific days and times when the chat button should be visible:
- Select business days (Mon-Sun)
- Set start and end times
- Supports overnight hours (e.g., 10 PM to 6 AM)

## 🎨 Customization Examples

### Basic Setup
```php
// Button appears on all pages after 3 seconds
Enable: ✓
Delay: 3 seconds
Position: Right
Colors: Default green (#25D366)
