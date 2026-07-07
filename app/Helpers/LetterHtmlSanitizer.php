<?php

namespace App\Helpers;

class LetterHtmlSanitizer
{
    protected static array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u',
        'ol', 'ul', 'li',
        'figure',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'div', 'span',
    ];

    public static function sanitize(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);

        $allowed = '<' . implode('><', self::$allowedTags) . '>';
        $html = strip_tags($html, $allowed);

        $html = preg_replace_callback('/<(\/?)(\w+)([^>]*)>/', function ($m) {
            $tag = strtolower($m[2]);
            $isClosing = $m[1] === '/';

            if (!in_array($tag, self::$allowedTags)) {
                return $isClosing ? '</' . $tag . '>' : '<' . $tag . '>';
            }

            if ($isClosing) {
                return '</' . $tag . '>';
            }

            $attrs = $m[3];
            $cleanAttrs = '';

            if (in_array($tag, ['p', 'div', 'span'])) {
                if (preg_match('/class\s*=\s*"([^"]*)"/i', $attrs, $cm)) {
                    $classes = array_filter(array_map('trim', explode(' ', $cm[1])), function ($c) {
                        return preg_match('/^ql-align-(center|right|justify)$/', $c) || preg_match('/^ql-indent-[1-9]$/', $c);
                    });
                    if (!empty($classes)) {
                        $cleanAttrs .= ' class="' . implode(' ', $classes) . '"';
                    }
                }
                if (preg_match('/style\s*=\s*"([^"]*)"/i', $attrs, $sm)) {
                    $allowedStyles = [];
                    if (preg_match('/text-align\s*:\s*(left|center|right)/i', $sm[1], $am)) {
                        $allowedStyles[] = 'text-align:' . strtolower($am[1]);
                    }
                    if (preg_match('/margin-left\s*:\s*\d+px/i', $sm[1], $mm)) {
                        $allowedStyles[] = trim($mm[0]);
                    }
                    if (!empty($allowedStyles)) {
                        $cleanAttrs .= ' style="' . implode('; ', $allowedStyles) . '"';
                    }
                }
            }

            if ($tag === 'figure') {
                if (preg_match('/class\s*=\s*"([^"]*)"/i', $attrs, $cm)) {
                    $classes = self::filterClasses($cm[1], ['table', 'no-border-table']);
                    if (!empty($classes) && in_array('table', $classes, true)) {
                        $cleanAttrs .= ' class="' . implode(' ', $classes) . '"';
                    }
                }
            }

            if (in_array($tag, ['table', 'td', 'th'])) {
                if (preg_match('/class\s*=\s*"([^"]*)"/i', $attrs, $cm)) {
                    $classes = self::filterClasses($cm[1], ['letter-table', 'no-border-table']);
                    if (!empty($classes)) {
                        $cleanAttrs .= ' class="' . implode(' ', $classes) . '"';
                    }
                }
                if (preg_match('/style\s*=\s*"([^"]*)"/i', $attrs, $sm)) {
                    $allowedStyles = [];
                    if (preg_match('/width\s*:\s*[\d.]+(?:%|px|em)?/i', $sm[1], $wm)) {
                        $allowedStyles[] = trim($wm[0]);
                    }
                    if (preg_match('/text-align\s*:\s*(left|center|right)/i', $sm[1], $am)) {
                        $allowedStyles[] = 'text-align:' . strtolower($am[1]);
                    }
                    if (!empty($allowedStyles)) {
                        $cleanAttrs .= ' style="' . implode('; ', $allowedStyles) . '"';
                    }
                }
                if (in_array($tag, ['td', 'th'])) {
                    if (preg_match('/colspan\s*=\s*"(\d+)"/i', $attrs, $cm)) {
                        $cleanAttrs .= ' colspan="' . (int)$cm[1] . '"';
                    }
                    if (preg_match('/rowspan\s*=\s*"(\d+)"/i', $attrs, $cm)) {
                        $cleanAttrs .= ' rowspan="' . (int)$cm[1] . '"';
                    }
                }
            }

            if (in_array($tag, ['tbody', 'tr'])) {
                if (preg_match('/class\s*=\s*"([^"]*)"/i', $attrs, $cm)) {
                    $classValue = trim(preg_replace('/\s+/', ' ', $cm[1]));
                    if ($classValue !== '') {
                        $cleanAttrs .= ' class="' . $classValue . '"';
                    }
                }
            }

            return '<' . $tag . $cleanAttrs . '>';
        }, $html);

        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace('> <', '><', $html);

        return trim($html);
    }

    public static function render(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $allowedTagPattern = '/<(p|br|strong|b|em|i|u|ol|ul|li|figure|table|thead|tbody|tfoot|tr|th|td|div|span)[\s>]/i';
        if (!preg_match($allowedTagPattern, $html)) {
            return self::toDisplayHtml($html);
        }

        return self::sanitize($html);
    }

    public static function toDisplayHtml(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        $paragraphs = preg_split('/\n\s*\n/', $text);
        $parts = [];

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === '') {
                continue;
            }

            $lines = explode("\n", $para);
            $escapedLines = array_map(function ($line) {
                return htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
            }, $lines);

            $parts[] = '<p>' . implode('<br>', $escapedLines) . '</p>';
        }

        return implode("\n", $parts);
    }

    private static function filterClasses(string $classValue, array $allowedClasses): array
    {
        $allowed = array_flip($allowedClasses);
        $classes = preg_split('/\s+/', trim($classValue)) ?: [];

        return array_values(array_filter(array_unique($classes), function ($class) use ($allowed) {
            return isset($allowed[$class]);
        }));
    }
}
