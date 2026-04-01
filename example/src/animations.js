/**
 * ICAP Theme Animations
 *
 * Site-specific orchestration built on top of the FX animation SDK.
 * Most elements are animated via CSS classes (.fx-*-pl / .fx-*-st) added in
 * Gutenberg — this file only handles cases that need custom selectors or
 * compound sequencing that classes alone can't express.
 *
 * Build: npm run build
 */
import { textReveal, reveal, scaleIn } from '@fancoolo/fx';

document.addEventListener('DOMContentLoaded', () => {

    // ── Quote block — two lines with staggered delay ──

    const quoteBlock = document.querySelector('.wp-block-fancoolo-quote');
    if (quoteBlock) {
        const firstLine = quoteBlock.querySelector('.quote-line--first');
        const lastLine  = quoteBlock.querySelector('.quote-line--last');

        if (firstLine) textReveal(firstLine, { trigger: 'scroll', scrollTrigger: { trigger: quoteBlock } });
        if (lastLine)  textReveal(lastLine,  { trigger: 'scroll', delay: 0.2, scrollTrigger: { trigger: quoteBlock } });
    }

    // ── "Join Us in Zurich" compound section ──

    const joinGlass = document.querySelector('.wp-block-fancoolo-glassbg.expend-none');
    if (joinGlass) {
        const joinParent = joinGlass.closest('section');
        if (joinParent) {
            scaleIn(joinGlass, { trigger: 'scroll', scrollTrigger: { trigger: joinParent } });

            const joinHeading = joinParent.querySelector('h2');
            if (joinHeading) {
                textReveal(joinHeading, { trigger: 'scroll', delay: 0.2, scrollTrigger: { trigger: joinParent } });
            }

            const subscribeBlock = joinParent.querySelector('.wp-block-fancoolo-subscribe');
            if (subscribeBlock) {
                reveal(subscribeBlock, { trigger: 'scroll', delay: 0.35, scrollTrigger: { trigger: joinParent } });
            }
        }
    }

    // ── Team members — staggered images + text ──

    const teamCards = document.querySelectorAll('.wp-block-fancoolo-team');
    if (teamCards.length > 0) {
        const teamParent = teamCards[0].closest('.wp-block-group');
        teamCards.forEach((card, i) => {
            const img = card.querySelector('img');
            if (img) reveal(img, { trigger: 'scroll', delay: i * 0.15, scrollTrigger: { trigger: teamParent } });

            card.querySelectorAll('p').forEach((p, j) => {
                textReveal(p, { trigger: 'scroll', delay: i * 0.15 + (j + 1) * 0.08, scrollTrigger: { trigger: teamParent } });
            });
        });
    }
});
