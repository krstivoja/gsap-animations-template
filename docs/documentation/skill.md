# Claude Code Skill

Fancoolo FX ships with a Claude Code skill so the AI assistant understands how to use the library when helping you write code.

## What the Skill Does

When installed, Claude Code will automatically:

- Know all 5 effects and their class names
- Understand trigger modes (`-pl`, `-st`, bare classes)
- Suggest correct modifier classes (`fx-duration-[n]`, etc.)
- Generate proper `FX.config` and `__FX_CONFIG__` code
- Use the JS API correctly for compound sequences
- Follow WordPress/Gutenberg best practices (classes only, no data attributes)
- Know how to add new effects following the existing pattern

## Installation

Copy the skill file into your Claude Code skills directory:

```bash
# macOS / Linux
mkdir -p ~/.claude/skills/fancoolo-fx
cp skills/SKILL.md ~/.claude/skills/fancoolo-fx/SKILL.md
```

```bash
# Windows
mkdir %USERPROFILE%\.claude\skills\fancoolo-fx
copy skills\SKILL.md %USERPROFILE%\.claude\skills\fancoolo-fx\SKILL.md
```

After copying, restart Claude Code. The skill will be available immediately.

## Verifying the Skill

Ask Claude Code something like:

> "Add a scroll-triggered text reveal to this heading"

Claude should respond with the correct `fx-text-reveal-st` class and explain the trigger behavior.

## What's Inside the Skill

The skill file (`skills/SKILL.md`) contains:

| Section | What Claude Learns |
|---------|-------------------|
| Effects table | All 5 effects, their classes, JS functions, defaults |
| Trigger modes | `-pl`, `-st`, bare classes, and tagMap |
| Modifier classes | `fx-duration-[n]`, `fx-delay-[n]`, `fx-stagger-[n]`, `fx-ease-[name]`, `fx-start-[pos]` |
| Config options | `sectionSelector`, `scrollStart`, `scrollOnce`, `tagMap` |
| JavaScript API | `FX.textReveal()`, `FX.reveal()`, etc. with full options |
| WordPress integration | `wp_enqueue_script` patterns and Gutenberg class usage |
| Architecture decisions | Why classes over data attributes, bracket syntax, no build step |
| Adding new effects | Step-by-step guide for extending the library |

## Updating the Skill

When Fancoolo FX gets new features or effects, update the skill file:

1. Edit `skills/SKILL.md` in the repo
2. Copy the updated file to `~/.claude/skills/fancoolo-fx/SKILL.md`
3. Restart Claude Code

## Using Without Installing

If you don't want to install the skill permanently, you can reference it in your project's `CLAUDE.md` file. Fancoolo FX already includes a `CLAUDE.md` at the project root that gives Claude context about the library.
