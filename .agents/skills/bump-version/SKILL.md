---
name: bump-version
description: Prepare an OpenGraph.xyz WordPress plugin version bump by updating the plugin header, readme version and stable tag, and changelog, then provide release commands. Use when the user asks to bump the plugin version or prepare a plugin release.
---

# Bump Plugin Version

Read [wordpress-plugin-release](../wordpress-plugin-release/SKILL.md) for the plugin's packaging and release rules.

You are helping release a new version of the **opengraph-xyz** WordPress plugin.

## Step 1 — Determine the version bump type

Use the version or bump type the user already specified. Otherwise, ask which bump they want before changing version files:

- `patch` — Bug fix (e.g. 1.5.4 → 1.5.5)
- `minor` — New feature, backwards-compatible (e.g. 1.5.4 → 1.6.0)
- `major` — Breaking change (e.g. 1.5.4 → 2.0.0)

## Step 2 — Read the current version

Read `opengraph-xyz.php` at the repository root and extract the current `Version:` value from the plugin header.

## Step 3 — Calculate the new version

Apply semver rules to the current version based on the user's answer:
- **patch** → increment the third segment (e.g. 1.5.3 → 1.5.4)
- **minor** → increment the second segment, reset third to 0 (e.g. 1.5.3 → 1.6.0)
- **major** → increment the first segment, reset second and third to 0 (e.g. 1.5.3 → 2.0.0)

## Step 4 — Collect changes since the last tag

From the repository root, identify the latest release tag with `git describe --tags --abbrev=0`, then use the commits since that tag to draft concise changelog bullet points:

```bash
git log <last-release-tag>..HEAD --oneline
```

Summarise the commits into human-readable changelog entries (e.g. "Fix plugin version header mismatch"). Include relevant uncommitted changes from the working tree when they are part of the requested release. If the release changes cannot be determined, ask the user what changed.

## Step 5 — Apply the version bump

Update **both** files together:

1. **`opengraph-xyz.php`** — change the `Version:` line in the plugin header to the new version.
2. **`readme.txt`** — change the `Version:` AND `Stable tag:` lines in the header block to the new version, then prepend a new changelog section directly below `== Changelog ==`:

```
= X.Y.Z =
* <bullet points from step 4>
```

## Step 6 — Walk through the deploy steps

After the files are updated, verify the plugin header, readme version, stable tag, and new changelog heading all agree. Follow the packaging checks in [wordpress-plugin-release](../wordpress-plugin-release/SKILL.md). Then print the following instructions for the user to run from the repository root:

```bash
git add opengraph-xyz.php readme.txt
git commit -m "Bump version to X.Y.Z"
git push origin main
git tag X.Y.Z
git push origin X.Y.Z
```

Remind them to:
- Watch the **Deploy to WordPress.org** GitHub Action at `https://github.com/opengraph-xyz/opengraph-wp/actions`
- Verify the SVN tag at `https://plugins.svn.wordpress.org/opengraph-xyz/tags/X.Y.Z/opengraph-xyz.php`
- Confirm the plugin page at `https://wordpress.org/plugins/opengraph-xyz/` shows the new version (may take a few minutes to cache-bust)
