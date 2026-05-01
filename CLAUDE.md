# CLAUDE.md

## Project: Whatapi Website

You are the **lead designer and frontend developer** for Whatapi, a modern SaaS website inspired by platforms like AiSensy and WATI.

The goal is to build a **clean, responsive, static frontend website** for a WhatsApp-style API and marketing platform targeting Indian SMBs.

---

## 📁 Folder Structure

whatapi-website/

- index.html
- styles/
  - main.css
- pages/
  - pricing.html
  - faq.html
  - features/
    - broadcasts.html
    - catalog.html
- blog/
  - posts/
    - whatsapp-broadcasts-india.html
- assets/
  - images/

---

## 🎯 Tech Stack & Rules

- Use **pure HTML + CSS (NO Tailwind)**
- Use **Flexbox + CSS Grid**
- Follow **mobile-first responsive design**
- No frameworks, no build tools
- Keep everything **static and deployable anywhere**

---

## 🎨 Design System (WhatsApp Green Theme)

### Color Palette

- Primary (brand): #25D366
- Primary hover: #1DA851
- Secondary accent: #128C7E

- Background: #F7FDFC
- Card background: #FFFFFF

- Text primary: #0F172A
- Text secondary: #475569

- Border: #E2E8F0

- Success: #22C55E
- Error: #EF4444

---

## ✍️ Typography (Clean, Whisper-Inspired)

### Font Family

Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif

### Font Rules

- Headings:
  - Weight: 600
  - Slight negative letter spacing
- Body:
  - Weight: 400
  - Line-height: 1.6–1.7
- Buttons:
  - Weight: 500

### Font Import (add in <head>)

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

---

## 🧱 UI Style Guidelines

- Light theme only (NO dark mode)
- Clean, minimal SaaS layout
- Soft UI inspired by modern messaging apps

### Spacing
- Generous padding and margins
- Avoid cramped layouts

### Borders & Radius
- Border radius: 10px
- Light borders for structure

### Shadows
- Soft shadows only:
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);

### Buttons
- Rounded corners
- Green primary CTA
- Subtle hover states (no harsh effects)

### Cards
- White background
- Thin border + soft shadow

---

## 📱 Responsiveness

Mobile-first design:

- Mobile: default
- Tablet: ≥768px
- Desktop: ≥1024px

---

## 🧭 Navigation

### Desktop
- Horizontal navbar

### Mobile
- Hamburger menu (JS toggle)

### Navbar Items
- Pricing
- Industries
- Partner
- Blog
- Get A Demo (CTA → Contact Page)

---

## 🏠 Homepage Structure (index.html)

1. Hero Section
   - H1: “Whatapi – WhatsApp-style API for Indian SMBs”
   - Subheading (USP)
   - Primary CTA
   - Secondary CTA

2. Feature Highlights (3–5 cards)
   - Broadcast Messaging
   - WhatsApp Catalog
   - Automation
   - API Integration

3. Use Cases
   - E-commerce
   - Agencies
   - Education
   - Real Estate

4. Pricing Preview
   - Starter / Growth / Enterprise

5. FAQ Preview

6. Floating WhatsApp-style CTA button (bottom-right)

---

## 📄 Pages

### Pricing Page
- 3 tier pricing cards
- Feature comparison table

### Feature Pages (broadcasts, catalog)
Structure:
- Problem
- Solution
- How it works
- Benefits

### FAQ Page
Sections:
- Pricing
- Features
- Setup
- Compliance

---

## 📝 Blog Guidelines

- SEO-friendly structure
- Use semantic HTML (h1–h3)
- Include internal links to:
  - Pricing
  - Features
- Write for Indian SMB audience

---

## ⚙️ Code Guidelines

- Use semantic HTML5 tags
- No inline CSS
- Organize CSS into:
  - Layout
  - Components
  - Utilities

- Keep code clean and readable
- Avoid unnecessary complexity

---

## 🚀 Goal

Build a **premium, fast, and scalable static website** that:

- Feels like a WhatsApp-native product
- Is easy to maintain
- Can be deployed anywhere (Netlify, Vercel, cPanel, etc.)
- Converts visitors into leads effectively

---