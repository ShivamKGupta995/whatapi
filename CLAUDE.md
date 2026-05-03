# CLAUDE.md

## Project: Whatapi Website

You are the **lead designer and frontend developer** for Whatapi, a modern SaaS website inspired by AiSensy and WATI.

The goal is to build a **clean, responsive, static frontend website** for a WhatsApp API and marketing platform targeting Indian SMBs.

---

## 📁 Folder Structure (Current State)

```
whatapi-website/
├── index.html
├── CLAUDE.md
├── styles/
│   ├── main.css          # global design system — used by all pages
│   ├── blog.css          # blog-specific styles
│   └── industries.css    # industries page styles
├── pages/
│   ├── pricing.html
│   ├── faq.html
│   ├── industries.html
│   ├── partner.html
│   └── contact.html
├── blog/
│   ├── index.html        # blog listing (fetches posts via API)
│   └── post.html         # single post renderer (fetches via API)
├── backend/              # PHP blog CMS backend
│   ├── config.php
│   ├── admin/            # dashboard, editor, login, delete, logout
│   ├── api/              # posts, post, categories, sitemap, upload
│   ├── includes/         # auth, db, helpers
│   └── setup/install.php
└── assets/
    └── images/
```

**Not yet built (planned):**
- `pages/features/broadcasts.html`
- `pages/features/catalog.html`

---

## 🎯 Tech Stack & Rules

- **Pure HTML + CSS — NO Tailwind, NO frameworks, NO build tools**
- Flexbox + CSS Grid for layout
- Mobile-first responsive design
- Static and deployable on Netlify, Vercel, or cPanel

---

## 🎨 Design System (WhatsApp Green Theme)

### CSS Custom Properties (defined in `styles/main.css`)

```css
--color-primary:       #25D366;
--color-primary-hover: #1DA851;
--color-accent:        #128C7E;
--color-bg:            #F7FDFC;
--color-card:          #FFFFFF;
--color-text:          #0F172A;
--color-text-muted:    #475569;
--color-border:        #E2E8F0;
--radius:              10px;
```

---

## ✍️ Typography

### Font

```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
```

Font family: `Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif`

### Weights — ONLY these three:

| Element | Weight |
|---|---|
| Headings (h1–h4) | 600 |
| Buttons | 500 |
| Body / labels | 400 |

**Never use font-weight: 700 or 800 anywhere.** The `strong` and `b` tags are overridden to `font-weight: 600` in `main.css`.

### Heading style
- `font-weight: 600`
- Slight negative `letter-spacing` (e.g., `-0.02em`)

### Body
- `line-height: 1.6–1.7`

---

## 🧱 UI Style Guidelines

- Light theme only — NO dark mode
- Clean, minimal SaaS layout

### Shadows
```css
box-shadow: 0 4px 20px rgba(0,0,0,0.05);
```

### Borders & Radius
- `border-radius: var(--radius)` → 10px
- Light `1px solid var(--color-border)` borders on cards

### Buttons
- Primary: `background: var(--color-primary)`, `color: #fff`, hover → `--color-primary-hover`
- Secondary / white: `background: #fff`, `color: var(--color-text)`, subtle border + shadow
- All buttons: `font-weight: 500`, `border-radius: 8px`

### Cards
- `background: #fff`
- `border: 1px solid var(--color-border)`
- `box-shadow: 0 4px 20px rgba(0,0,0,0.05)`

---

## 📱 Responsiveness

Mobile-first:

| Breakpoint | Min-width |
|---|---|
| Mobile | default |
| Tablet | 768px |
| Desktop | 1024px |

---

## 🧭 Navigation

### Navbar items (exact order)

| Label | URL | Notes |
|---|---|---|
| Pricing | `/pages/pricing.html` | |
| Industries | `/pages/industries.html` | |
| Partner | `/pages/partner.html` | |
| Blog | `/blog/index.html` | |
| Get a Demo | `/pages/contact.html` | Primary CTA — green button |

### Desktop
- Horizontal sticky navbar

### Mobile
- Hamburger icon (SVG, not emoji) toggles nav
- JS adds `.nav-open` class + locks `body` scroll
- Mobile menu CTA: "Get a Demo" → `/pages/contact.html`

---

## 💰 Pricing (Authoritative — use these everywhere)

### Platform Plans

| Plan | Price | Yearly |
|---|---|---|
| Free Forever | ₹0 | — |
| Starter | ₹1,399/mo | ₹1,259/mo |
| Growth | ₹2,999/mo | ₹2,699/mo |
| Enterprise | Custom | Custom |

### Key Plan Details

**Free Forever**
- 1,000 free service conversations/month (Meta's default)
- 1 WhatsApp number, 2 team members
- Basic dashboard

**Starter (₹1,399/mo)**
- Unlimited team members
- Broadcast campaigns
- WhatsApp Catalog
- Basic analytics
- Email support

**Growth (₹2,999/mo)** ← Most Popular
- Everything in Starter
- Advanced analytics & reports
- API access & webhooks
- Priority support
- Zapier & CRM integrations

**Enterprise (Custom)**
- Everything in Growth
- Dedicated account manager
- Custom integrations
- SLA guarantee
- Onboarding support

### Per-Message Pricing (Meta WhatsApp Cloud API — India rates)

| Message Type | Rate |
|---|---|
| Marketing | ₹1.09 |
| Utility | ₹0.145 |
| Authentication | ₹0.145 |
| Service | FREE |

These are billed by Meta separately on top of platform plans.

### Add-ons
- **Chatbot Flows**: ₹2,500 for 5 flows — sold separately, NOT included in any plan

### Billing
- Pre-paid monthly billing
- Yearly plans available at ~10% discount
- Billed in INR (Indian Rupees)

---

## 🏠 Homepage Structure (index.html)

1. **Hero** — H1: "Whatapi – WhatsApp-style API for Indian SMBs", primary CTA + secondary CTA (both → `/pages/contact.html`)
2. **Stats bar** — 5,000+ businesses, 98% delivery, 50M+ messages, 99.9% uptime
3. **Feature Highlights** — Broadcasts, Catalog, Automation, API Integration (SVG icons)
4. **Use Cases** — E-commerce, Agencies, Education, Real Estate (SVG icons, links → `/pages/industries.html`)
5. **Pricing Preview** — Free Forever banner + Starter / Growth / Enterprise cards
6. **FAQ Preview** — 5 accordion items, "View All FAQs" → `/pages/faq.html`
7. **CTA Banner** — both buttons → `/pages/contact.html`
8. **Footer** — links to all pages
9. **Floating WA button** — bottom-right, links → `/pages/contact.html`

---

## 📄 Pages

### Pricing Page (`pages/pricing.html`)
- Billing toggle (monthly / yearly) with JS
- 4 plan cards (Free Forever, Starter, Growth★, Enterprise)
- Per-message pricing section (6 message type cards with SVG icons)
- Plan comparison table
- FAQ section (10 questions about per-message pricing)
- CTA: all buttons → `/pages/contact.html`

### FAQ Page (`pages/faq.html`)
- 4 sections with accordion JS (open one at a time):
  1. Pricing (7 questions)
  2. Features (6 questions)
  3. Setup (5 questions)
  4. Compliance (5 questions)
- Each section has an SVG category icon
- CTA banner at bottom

### Industries Page (`pages/industries.html`)
- 4 industry sections: E-commerce, Real Estate, Education, Agencies
- Each: Problem → WhatsApp solution → CTA
- Uses `styles/industries.css`

### Partner Page (`pages/partner.html`)
- Who can partner, benefits, commission model
- CTA: "Become a Partner" → `/pages/contact.html`

### Contact Page (`pages/contact.html`)
- Demo request / lead capture form
- Primary destination for all site CTAs

---

## 📝 Blog

- `blog/index.html` — listing page, fetches posts from PHP API
- `blog/post.html` — single post renderer, fetches post by slug from PHP API
- `backend/api/posts.php`, `post.php`, `categories.php` — REST endpoints
- `backend/admin/` — protected CMS for writing/editing posts
- SEO-friendly: semantic HTML (h1–h3), internal links to Pricing and feature pages
- Content focus: Indian SMB WhatsApp use cases

---

## 🎨 Icon System — SVG Only (No Emoji as Icons)

**Rule: Never use emoji characters as visual icons anywhere on the site.**

Only ✓ (U+2713) and ✗ (U+2717) Unicode text symbols are acceptable in plan feature lists — these are typographic, not emoji.

### Standard card/section icon format

```html
<svg width="26" height="26" viewBox="0 0 24 24" fill="none"
     stroke="var(--color-accent)" stroke-width="1.8"
     stroke-linecap="round" stroke-linejoin="round">
  <!-- paths here -->
</svg>
```

### Size guide

| Context | width/height | stroke-width |
|---|---|---|
| Section / card icons | 26px | 1.8 |
| Inline (buttons, nav) | 16px | 2 |
| Blog placeholders / thumbnails | 48–64px | 1.5 |

### Inline SVGs inside text (e.g., chat message mockups)

For SVGs that appear inline within a sentence or chat bubble, add:

```html
<svg style="display:inline;vertical-align:middle" width="14" height="14" ...>
```

### Dynamic icons in JS (blog)

`blog/index.html` and `blog/post.html` define a `BLOG_ICONS` map:

```js
const BLOG_ICONS = {
  '📢': `<svg ...>...</svg>`,
  '📝': `<svg ...>...</svg>`,
  // etc.
};
// Usage in template literals:
`${BLOG_ICONS[p.emoji] || BLOG_ICONS['📝']}`
```

### Icon source style
Feather Icons / Lucide — stroke-based, 24×24 viewBox.

---

## ⚙️ Code Guidelines

- Semantic HTML5
- No inline CSS (use class-based CSS in stylesheets)
- CSS organized into: Layout → Components → Utilities
- Minimal JS: only for navbar toggle, FAQ accordion, pricing billing toggle
- Clean, readable code — no comments unless the WHY is non-obvious

---

## 🔗 CTA Convention

**All primary CTAs across the site point to `/pages/contact.html`** (demo request / lead capture).

No external dashboard links are used in the public site at this stage.

---

## 🚀 Goal

Build a **premium, fast, and scalable static website** that:

- Feels like a WhatsApp-native product
- Is easy to maintain
- Deploys anywhere (Netlify, Vercel, cPanel)
- Converts visitors into leads effectively
