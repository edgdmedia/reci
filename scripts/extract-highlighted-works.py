#!/usr/bin/env python3
"""Recover Highlighted Contributions from the scraped Pitt profile pages.

The JSON export flattened every entry into one line and dropped the anchors,
which is why these render as a single link. The saved HTML still has both, so
this reads that instead and emits structured entries.

    python3 scripts/extract-highlighted-works.py [--report]

Writes docs/content/collaborators/highlighted-works.json. Reads only; nothing
here touches the database.
"""

from __future__ import annotations

import argparse
import glob
import html
import json
import os
import re
import sys

HTML_DIR = "docs/content/collaborators/html"
OUT = "docs/content/collaborators/highlighted-works.json"

# The Drupal field that holds this section on the source site.
SECTION = re.compile(
    r'field--name-field-publications.*?<div class="field__item">(.*?)</div>', re.S
)
ANCHOR = re.compile(r'<a\s[^>]*href="(https?://[^"]+)"[^>]*>(.*?)</a>', re.S)
PARA = re.compile(r"<p[^>]*>(.*?)</p>", re.S)
BREAK = re.compile(r"<br\s*/?>", re.I)


def text_of(fragment: str) -> str:
    """Visible text of an HTML fragment, whitespace collapsed."""
    return re.sub(r"\s+", " ", html.unescape(re.sub(r"<[^>]+>", " ", fragment))).strip()


# Navigational link text, not a name for the thing being linked to.
GENERIC_LABELS = {
    "here", "link", "links", "view", "watch", "listen", "read", "read more",
    "click here", "watch interview", "listen here", "watch video", "view here",
    "download", "more", "website", "site", "article",
}


def looks_like_url(value: str) -> bool:
    """True for anchor text that is really an address.

    The source is not consistent: some anchors carry a bare URL, and at least
    one is a URL with its leading character lost ("ttps://..."). Matching only
    on a clean "http" prefix let those through as if they were titles.
    """
    probe = value.strip().lower()

    return bool(
        re.match(r"^h?t{1,2}ps?://", probe)
        or probe.startswith("www.")
        or "://" in probe
        or re.match(r"^[a-z0-9-]+(\.[a-z0-9-]+)+/", probe)
    )


def usable_title(value: str) -> str | None:
    """Anchor text only counts as a title if a reader could use it."""
    value = value.strip()

    if not value or looks_like_url(value) or value.lower().strip(" .:") in GENERIC_LABELS:
        return None

    return value


def clean_note(value: str) -> str | None:
    """Drop residue that is only punctuation once the anchors are removed."""
    value = value.strip(" -–—:;,.\u00a0")

    return value or None


def parse_entry(block: str) -> list[dict]:
    """One paragraph becomes one entry per link, or one citation.

    Emitting per link matters: five profiles list several links in a single
    paragraph, and keeping only the first would have silently dropped twelve of
    them. The paragraph's prose goes to the first entry, since that is what it
    describes.

    A bare URL used as its own anchor text is not a title, so it is discarded
    rather than shown to a reader as if it were one.
    """
    anchors = ANCHOR.findall(block)
    residue = clean_note(text_of(ANCHOR.sub(" ", block)))

    if not anchors:
        return [{"kind": "citation", "url": None, "title": None, "note": residue}] if residue else []

    entries = []
    for index, (url, raw_label) in enumerate(anchors):
        title = usable_title(text_of(raw_label))

        entries.append(
            {
                "kind": "link",
                "url": url,
                "title": title,
                "note": residue if index == 0 else None,
            }
        )

    return entries


def parse_file(path: str) -> dict | None:
    with open(path, encoding="utf-8", errors="ignore") as handle:
        source = handle.read()

    section = SECTION.search(source)
    if not section:
        return None

    body = section.group(1)
    blocks = PARA.findall(body) or [body]

    entries = []
    for block in blocks:
        for piece in BREAK.split(block):
            entries.extend(parse_entry(piece))

    if not entries:
        return None

    name = text_of(re.search(r"<title>(.*?)</title>", source, re.S).group(1)) if "<title>" in source else ""

    return {
        "slug": os.path.splitext(os.path.basename(path))[0],
        "name": name.split("|")[0].strip(),
        "entries": entries,
    }


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--report", action="store_true", help="print a summary and samples")
    args = parser.parse_args()

    profiles = [p for p in (parse_file(f) for f in sorted(glob.glob(f"{HTML_DIR}/*.html"))) if p]

    with open(OUT, "w", encoding="utf-8") as handle:
        json.dump({"count": len(profiles), "profiles": profiles}, handle, indent=2, ensure_ascii=False)

    entries = [e for p in profiles for e in p["entries"]]
    links = [e for e in entries if e["kind"] == "link"]

    print(f"profiles      : {len(profiles)}")
    print(f"entries       : {len(entries)}")
    print(f"  links       : {len(links)}")
    print(f"    titled    : {sum(1 for e in links if e['title'])}")
    print(f"    bare url  : {sum(1 for e in links if not e['title'])}")
    print(f"  citations   : {sum(1 for e in entries if e['kind'] == 'citation')}")
    print(f"profiles with >1 entry: {sum(1 for p in profiles if len(p['entries']) > 1)}")
    print(f"\nwrote {OUT}")

    if args.report:
        for profile in profiles[:3]:
            print(f"\n--- {profile['slug']} ({len(profile['entries'])} entries) ---")
            for entry in profile["entries"][:4]:
                print(f"  [{entry['kind']}] title={entry['title']!r}")
                print(f"           url={entry['url']}")
                if entry["note"]:
                    print(f"           note={entry['note'][:70]!r}")

    return 0


if __name__ == "__main__":
    sys.exit(main())
