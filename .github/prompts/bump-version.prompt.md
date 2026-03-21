---
agent: agent
description: Bump the plugin version, update readme.txt Stable tag and Changelog, then walk through the deploy steps.
---

You are helping release a new version of the **opengraph-xyz** WordPress plugin.

## Step 1 — Determine the version bump type

Ask the user:

> Is this a **patch** (bug fix), **minor** (new feature, backwards-compatible), or **major** (breaking change) release?

Wait for their answer before proceeding.

## Step 2 — Read the current version

Read `/Users/antonio/repos/opengraph-wp/opengraph-xyz.php` and extract the current `Version:` value from the plugin header.

## Step 3 — Calculate the new version

Apply semver rules to the current version based on the user's answer:
- **patch** → increment the third segment (e.g. 1.5.3 → 1.5.4)
- **minor** → increment the second segment, reset third to 0 (e.g. 1.5.3 → 1.6.0)
- **major** → increment the first segment, reset second and third to 0 (e.g. 1.5.3 → 2.0.0)

## Step 4 — Collect changes since the last tag

Run the following command and use its output to draft concise changelog bullet points:

```bash
cd /Users/antonio/repos/opengraph-wp && git log $(git describe --tags --abbrev=0)..HEAD --oneline
```

Summarise the commits into human-readable changelog entries (e.g. "Fix plugin version header mismatch"). If there are no commits beyond the last tag, ask the user what changed.

## Step 5 — Apply the version bump

Update **both** files atomically:

1. **`opengraph-xyz.php`** — change the `Version:` line in the plugin header to the new version.
2. **`readme.txt`** — change the `Version:` AND `Stable tag:` lines in the header block to the new version, then prepend a new changelog section directly below `== Changelog ==`:

```
= X.Y.Z =
* <bullet points from step 4>
```

Show the user the proposed changelog entry and version numbers before making any file changes, and ask for confirmation.

## Step 6 — Walk through the deploy steps

After the files are updated, print the following instructions for the user to run in their terminal:

```bash
cd /Users/antonio/repos/opengraph-wp
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
