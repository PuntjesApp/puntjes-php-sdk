#!/bin/sh
# Fails when the public API of this checkout differs from the latest release in a breaking way, unless the change
# is marked as breaking. Needs the full git history and the tags. Run in CI on pull requests only.
# Environment: PR_TITLE, PR_BODY, HEAD_REF (set by CI); BASE_REF (default origin/main).
set -eu
base_ref=${BASE_REF:-origin/main}
tag=$(git tag --list 'v[0-9]*' --sort=-v:refname | head -n 1)
breaking='^[a-z]+(\([^)]*\))?!:|^BREAKING[ -]CHANGE:'

case "${HEAD_REF:-}" in
  release-please--*) echo "Skipped: a release PR holds changes that were already reviewed."; exit 0 ;;
esac
if printf '%s\n%s\n' "${PR_TITLE:-}" "${PR_BODY:-}" | grep -Eq "$breaking"; then
  echo "Skipped: the PR is marked as breaking."; exit 0
fi
if git log "$tag..$base_ref" --format='%s%n%b' | grep -Eq "$breaking"; then
  echo "Skipped: $base_ref already has a breaking change, so the next release is a major."; exit 0
fi

if roave-backward-compatibility-check --from="$tag" --to=HEAD --format=github-actions; then
  exit 0
fi
echo "::error::This PR breaks the public API of $tag. Retitle it 'feat!: ...' (or add a 'BREAKING CHANGE:' line to the body)," \
  "so the next release is a major. For a regenerate PR, also run 'gh pr merge <number> --disable-auto' and ask for a review."
exit 1
