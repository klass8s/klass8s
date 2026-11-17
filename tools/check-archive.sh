#!/usr/bin/env bash
# Fails when the archive Composer would install from leaves out a file the package needs, or
# carries the tests. The archive is made from the index, with the export rules the index holds,
# so a rule is checked before it is committed as well as after.
set -euo pipefail
cd "$(dirname "$0")/.."

# What a consumer needs: composer.json and what it autoloads; phaenon.json and the sources it
# commits outputs for, which a Phaenon project reads beside those outputs; the directories
# Klass8sExtension loads at runtime (config/services.yaml, the make commands' templates/); and
# what the license asks every copy to carry.
needed=(composer.json phaenon.json config templates LICENSE NOTICE)
# Read into a variable first: a failing command in a process substitution would go unnoticed.
if ! derived=$(php -r '
    $composer = json_decode(file_get_contents("composer.json"), true, flags: JSON_THROW_ON_ERROR);
    $phaenon = json_decode(file_get_contents("phaenon.json"), true, flags: JSON_THROW_ON_ERROR);
    $dirs = array_merge(array_values($composer["autoload"]["psr-4"] ?? []), $phaenon["committed"] ?? []);
    echo implode("\n", array_map(static fn (string $dir): string => rtrim($dir, "/"), $dirs)), "\n";
'); then
    echo "Could not read the package's directories from composer.json and phaenon.json."
    exit 1
fi
while IFS= read -r dir; do
    [[ -n "$dir" ]] && needed+=("$dir")
done <<<"$derived"
for dir in .src src; do
    if [[ " ${needed[*]} " != *" $dir "* ]]; then
        echo "composer.json and phaenon.json no longer name $dir, which the package ships; the check would pass without it."
        exit 1
    fi
done

archived=$(git archive "$(git write-tree)" | tar -t | grep -v '/$' | sort)
expected=$(git ls-files -- "${needed[@]}" | sort)
failed=0

missing=$(comm -23 <(printf '%s\n' "$expected") <(printf '%s\n' "$archived"))
if [[ -n "$missing" ]]; then
    echo "The archive leaves out $(wc -l <<<"$missing" | tr -d ' ') file(s) the package needs, among them:"
    head -n 10 <<<"$missing" | sed 's/^/  /'
    failed=1
fi

tests=$(grep -E '^\.?tests/' <<<"$archived" || true)
if [[ -n "$tests" ]]; then
    echo "The archive carries the tests, among them:"
    head -n 3 <<<"$tests" | sed 's/^/  /'
    failed=1
fi

exit "$failed"
