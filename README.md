# ZOS fixes: locked article (detail-module)

Visual fixes to the locked article: the guest view of `detail-module`, shown when a visitor isn't logged in.

Base: `soz/171-1-zos-oftalmologi`, branch `develop` at `a127c11d`.
Drop-in path in that repo: `source/components/03-modules/detail-module/`.

## What changed

### 1. Removed the old blurs and gradient

- Removed the four `backdrop-filter` blur layers (1.5px, 6px, 14px, 28px) on `__footer::before/::after` and `__fade::before/::after`.
- Removed the `__fade` element and its off-white gradient band (`rgba(248, 250, 252, 0.65)`).

### 2. New progressive blur

- New `detail-module__blur` element. It's rendered only in the guest view, as the first child of `__body`.
- It's 16px at the cut-off edge and eases to 0 at the top. That's six stacked layers (0.5, 1, 2, 4, 8, 16px), each masked to an overlapping band so the change is gradual.
- It covers the lower half of the visible text. Its height is half the distance from the top of the first content item to the cut-off, so the featured image isn't counted.
- The height is set by JS as `--blur-height` on the module and recalculated on resize. If the JS doesn't run, it falls back to 25% of the visible body.
- `__body` gets `position: relative` in the guest view so the blur anchors to the cut-off edge.

### 3. Colour fade under the blur

- A `::before` on `__blur` covers the same area beneath the blur layers.
- It goes from the page background (`$cgp_gray_50`, `#F8FAFC`) at the cut-off edge to the same colour at 0% opacity at the top.
- It uses the SCSS colour value, so it won't follow a different section background.

### 4. Button position

- The registration button now sits directly under the cut-off edge, with no gap, instead of overlapping the last content block.
- Removed the footer's negative `margin-top`, fixed height (448px desktop / 360px tablet and down), `position` and `z-index`. The 65px bottom padding stays.
- Removed the `--overlay-height` measurement from the JS, since nothing uses it anymore.

### 5. Fractal test variants

Added locked-article variants to `detail-module.config.json` for testing different article lengths and content:

`guest-very-short`, `guest-short`, `guest-long`, `guest-no-image`, `guest-gallery-first`, `guest-video-first`, `guest-long-title`

The existing `default` and `guest-user` variants are unchanged.

## Files

| File | Change |
| --- | --- |
| `detail-module.scss` | Old blurs, fade and footer overlap removed; new `__blur` with progressive blur and colour fade |
| `detail-module.twig` | `__fade` removed; `__blur` added (guest view only) |
| `detail-module.js` | Sets `--blur-height` instead of `--overlay-height` |
| `detail-module.config.json` | Seven new guest test variants |
| `detail-module.acf.php` | Unchanged, included so the module is complete |

## Known limitations

- **Short articles:** if the article fits on screen, nothing is actually cut off, but the blur and fade still cover the lower half of the text.
- **Gallery or video first:** if the first content item is a gallery or video, the blur measures from that item, not from the first text.

## Checks

- Stylelint and ESLint pass on the changed files.
- Builds in Fractal (`pnpm run dev`), and every variant above renders.
