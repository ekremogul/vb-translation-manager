<?php

namespace EkremOgul\VbTranslateManager\Commands;

use EkremOgul\VbTranslateManager\Models\VbTranslateManagerKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class VbTranslateManagerCommand extends Command
{
    public $signature = 'vb-translate-manager';

    public $description = 'My command';

    private $types = [];
    private $paths = [];
    private $excepts = [];

    private $founds = [];

    public function handle(): int
    {
        $this->types = config('vb-translate-manager.types');
        $this->paths = config('vb-translate-manager.paths');
        $this->excepts = config('vb-translate-manager.excepts');
        $functions = [
            "lang",
            "__"
        ];
        $groupPattern =                          // See https://regex101.com/r/WEJqdL/6
            "[^\w|>]" .                          // Must not have an alphanum or _ or > before real method
            '(' . implode('|', $functions) . ')' .  // Must start with one of the functions
            "\(" .                               // Match opening parenthesis
            "[\'\"]" .                           // Match " or '
            '(' .                                // Start a new group to match:
            '[\/a-zA-Z0-9_-]+' .                 // Must start with group
            "([.](?! )[^\1)]+)+" .               // Be followed by one or more items/keys
            ')' .                                // Close group
            "[\'\"]" .                           // Closing quote
            "[\),]";                             // Close parentheses or new parameter

        $stringPattern =
            "[^\w]".                                     // Must not have an alphanum before real method
            '('.implode('|', $functions).')'.             // Must start with one of the functions
            "\(\s*".                                       // Match opening parenthesis
            "(?P<quote>['\"])".                            // Match " or ' and store in {quote}
            "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)". // Match any string that can be {quote} escaped
            "\k{quote}".                                   // Match " or ' previously matched
            "\s*[\),]";

        $groupKeys = collect([]);
        $stringKeys = collect([]);


            $finder = new Finder();

            $except = collect($this->excepts);
            $finder->files()->in($this->paths)
//                ->notPath(["Filament","vendor","filament"])
                ->name("*.php");
            foreach ($finder as $file) {
                $filePath = $file->getPathname();
                $check = $except->filter(function ($item) use ($filePath) {
                    return str($filePath)->startsWith($item);
                });
                if($check->count())
                    continue;

                if (preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                    // Get all matches
                    foreach ($matches[2] as $key) {
                        if($groupKeys->where('key', $key)->count() < 1)
                            $groupKeys->add(["key" => $key, "file" => $file->getPathname()]);
                    }
                }
                if (preg_match_all("/$stringPattern/siU", $file->getContents(), $matches)) {
                    foreach ($matches['string'] as $key) {
                        if (preg_match("/(^[\/a-zA-Z0-9_-]+([.][^\1)\ ]+)+$)/siU", $key, $groupMatches)) {
                            // group{.group}.key format, already in $groupKeys but also matched here
                            // do nothing, it has to be treated as a group
                            continue;
                        }

                        //TODO: This can probably be done in the regex, but I couldn't do it.
                        //skip keys which contain namespacing characters, unless they also contain a
                        //space, which makes it JSON.
                        if (! (Str::contains($key, '::') && Str::contains($key, '.'))
                            || Str::contains($key, ' ')) {
                            if($stringKeys->where('key', $key)->count() < 1)
                                $stringKeys->add(["key" => $key, "file" => $file->getPathname()]);
                        }
                    }
                }
            }
            $allTranslates =array_merge($groupKeys->toArray(), $stringKeys->toArray());
//            $allTranslates = collect($allTranslates);
//            dd($allTranslates->take(10)->toArray());
        VbTranslateManagerKey::truncate();
            VbTranslateManagerKey::query()->insert($allTranslates);

        return self::SUCCESS;
    }
}
