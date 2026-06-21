---
name: FNI System
colors:
  surface: '#f8f9fa'
  surface-dim: '#d9dadb'
  surface-bright: '#f8f9fa'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f4f5'
  surface-container: '#edeeef'
  surface-container-high: '#e7e8e9'
  surface-container-highest: '#e1e3e4'
  on-surface: '#191c1d'
  on-surface-variant: '#444653'
  inverse-surface: '#2e3132'
  inverse-on-surface: '#f0f1f2'
  outline: '#757684'
  outline-variant: '#c4c5d5'
  surface-tint: '#3755c3'
  primary: '#00288e'
  on-primary: '#ffffff'
  primary-container: '#1e40af'
  on-primary-container: '#a8b8ff'
  inverse-primary: '#b8c4ff'
  secondary: '#555f6d'
  on-secondary: '#ffffff'
  secondary-container: '#d6e0f1'
  on-secondary-container: '#596372'
  tertiary: '#611e00'
  on-tertiary: '#ffffff'
  tertiary-container: '#872d00'
  on-tertiary-container: '#ffa583'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dde1ff'
  primary-fixed-dim: '#b8c4ff'
  on-primary-fixed: '#001453'
  on-primary-fixed-variant: '#173bab'
  secondary-fixed: '#d9e3f4'
  secondary-fixed-dim: '#bdc7d8'
  on-secondary-fixed: '#121c28'
  on-secondary-fixed-variant: '#3e4755'
  tertiary-fixed: '#ffdbce'
  tertiary-fixed-dim: '#ffb59a'
  on-tertiary-fixed: '#380d00'
  on-tertiary-fixed-variant: '#802a00'
  background: '#f8f9fa'
  on-background: '#191c1d'
  surface-variant: '#e1e3e4'
  trust-blue-light: '#EFF6FF'
  trust-blue-dark: '#1E3A8A'
  status-real: '#059669'
  status-real-light: '#ECFDF5'
  status-fake: '#DC2626'
  status-fake-light: '#FEF2F2'
  status-uncertain: '#D97706'
  status-uncertain-light: '#FFFBEB'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-xl-mobile:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 34px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  result-label:
    fontFamily: Inter
    fontSize: 48px
    fontWeight: '800'
    lineHeight: 48px
    letterSpacing: 0.05em
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-bold:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-max: 960px
  card-max: 720px
  gutter: 24px
  margin-mobile: 16px
  stack-lg: 48px
  stack-md: 24px
  stack-sm: 12px
---

## Brand & Style

The design system is built on a foundation of **Corporate / Modern** principles, prioritizing authority, clarity, and rapid comprehension. The brand personality is calm and institutional, aiming to evoke a sense of objective truth and reliability in an era of digital misinformation.

The visual style utilizes a refined card-based architecture with significant whitespace to reduce cognitive load. It avoids "playful" elements, opting instead for a professional aesthetic inspired by high-end technical tools like Stripe and Linear. The interface uses high-contrast typography and functional color coding to ensure that the AI's verdict—the most critical information—is communicated with absolute clarity.

- **Minimalist approach:** Every element serves a functional purpose.
- **Data-first hierarchy:** The UI recedes to allow the analysis results to take center stage.
- **Trust-building visuals:** Uses subtle shadows and clear borders to create a stable, grounded environment.

## Colors

The palette is anchored by **Trust Blue** (#1E40AF), a deep, authoritative shade that signals stability. The background uses a very light neutral gray to provide a clean, distraction-free canvas.

Functional colors are critical for the system's utility:
- **Emerald Green (Status Real):** Used for positive identification. It must be paired with high-contrast text to remain accessible.
- **Red (Status Fake):** Used for debunked content. 
- **Amber (Status Uncertain):** Used when the AI lacks sufficient confidence.

Each status color includes a lighter tint for background fills in result cards, ensuring that the color signal is unmistakable without being visually aggressive. For accessibility, never rely on color alone; always accompany status colors with definitive iconography and clear labeling.

## Typography

This design system exclusively uses **Inter** to maintain a systematic and utilitarian feel. The hierarchy is strictly enforced to guide the user from the high-level hero message down to the granular details of the AI analysis.

**Key Roles:**
- **Headline XL:** Reserved for the main value proposition on the landing page.
- **Result Label:** Ultra-bold and all-caps to provide an instantaneous verdict.
- **Body LG:** Used for article previews and main descriptions to ensure comfortable long-form reading.
- **Label Caps:** Used for metadata like character counts, confidence levels, and timestamps.

On mobile devices, headline sizes are scaled down to maintain balance, while body text remains at 16px-18px for maximum legibility.

## Layout & Spacing

The layout follows a **Fixed Grid** philosophy for the central interaction area to ensure the tool feels focused and contained. 

- **Desktop:** The main content is centered within a 960px container. The input card is restricted to 720px to prevent the text areas from becoming too wide for comfortable typing and reading.
- **Tablet:** Margins reduce to 24px, and the input card expands to fill the available width.
- **Mobile:** Margins reduce to 16px. All buttons transition to full-width to accommodate thumb-reach zones.

A consistent 8px-based spacing rhythm is used. Vertical stacking between sections (e.g., Hero to Input Card) uses 48px (stack-lg) to create clear breathing room, while internal card elements use 12px or 24px (stack-sm/md).

## Elevation & Depth

Hierarchy is established through **Tonal Layers** and **Ambient Shadows**.

1. **Base Layer:** The `gray-50` background provides a low-contrast foundation.
2. **Surface Layer:** White cards sit on top of the base.
3. **Shadows:** A single, soft, multi-layered shadow is used for cards (e.g., `box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)`). This creates a sense of "lift" without being distracting.
4. **Interactive State:** Primary buttons utilize a slightly deeper shadow on hover to provide tactile feedback.
5. **Result Reveal:** When the result appears, it should use a subtle vertical slide-in animation, suggesting it is physically "emerging" from the analysis process.

## Shapes

The shape language is **Rounded**, striking a balance between modern software design and professional sobriety. 

- **Cards:** Use `rounded-lg` (1rem / 16px) to feel approachable.
- **Buttons & Inputs:** Use `rounded-md` (0.5rem / 8px) for a more structured, precise look.
- **Status Pills:** Use `rounded-full` (pill-shaped) to distinguish them from interactive buttons.
- **Progress/Gauge Bars:** Use rounded ends to soften the data visualization.

## Components

### Buttons
- **Primary:** Trust Blue (#1E40AF) background, white text. Bold weight. Includes a leading icon for "Check Credibility."
- **Secondary:** Transparent background with a subtle gray-300 border or ghost style.
- **Disabled:** Light gray background with muted text; cursor set to `not-allowed`.

### Input Fields
- **Textarea:** Large, 1px border (#D1D5DB). Focus state uses a 2px Trust Blue ring. 
- **URL Input:** Includes a leading globe icon in the prefix area.
- **Validation:** Error states use a red-600 border and a small supporting text message below the field.

### Result Cards
- **Header Strip:** A 4px-6px top border colored by status (Green/Red/Amber).
- **Result Group:** The label and icon are the largest elements.
- **Confidence Gauge:** A horizontal track (gray-200) with a colored fill indicating the percentage (0-100%).

### Tabs (Mode Toggle)
- Non-active tabs have no background; the active tab uses a subtle light blue fill or a thick bottom border to indicate focus. Transition between tabs must be instantaneous but visually smooth.

### Navigation
- Sticky top bar with a glassmorphism effect (backdrop-blur) to maintain context while scrolling through long results or the "How It Works" section.