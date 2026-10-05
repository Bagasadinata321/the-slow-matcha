<?php

namespace App\Services;

class EditorjsParser
{
    /**
     * Konversi JSON / Array dari Editor.js menjadi HTML bersih.
     *
     * @param string|array|null $jsonContent
     * @return string
     */
    public static function parse($jsonContent): string
    {
        if (empty($jsonContent)) {
            return '';
        }

        // Jika data dikirim dalam bentuk string JSON, decode terlebih dahulu
        $data = is_array($jsonContent) ? $jsonContent : json_decode($jsonContent, true);

        // Validasi struktur JSON khas Editor.js
        if (!$data || !isset($data['blocks']) || !is_array($data['blocks'])) {
            return is_string($jsonContent) ? e($jsonContent) : '';
        }

        $htmlOutput = '';

        foreach ($data['blocks'] as $block) {
            $type = $block['type'] ?? '';
            $blockData = $block['data'] ?? [];

            switch ($type) {
                case 'header':
                    $level = min(max((int) ($blockData['level'] ?? 2), 1), 6);
                    $text = $blockData['text'] ?? '';
                    $htmlOutput .= "<h{$level} class=\"text-xl font-bold text-gray-900 mt-6 mb-3\">{$text}</h{$level}>";
                    break;

                case 'paragraph':
                    $text = $blockData['text'] ?? '';
                    $htmlOutput .= "<p class=\"text-gray-600 leading-relaxed mb-4\">{$text}</p>";
                    break;

                case 'list':
                    $style = ($blockData['style'] ?? 'unordered') === 'ordered' ? 'ol' : 'ul';
                    $listClass = $style === 'ol' ? 'list-decimal' : 'list-disc';
                    $items = $blockData['items'] ?? [];
                    
                    $htmlOutput .= "<{$style} class=\"{$listClass} list-inside space-y-1 my-4 text-gray-600 pl-2\">";
                    foreach ($items as $item) {
                        $htmlOutput .= "<li>{$item}</li>";
                    }
                    $htmlOutput .= "</{$style}>";
                    break;

                case 'image':
                    $url = $blockData['file']['url'] ?? ($blockData['url'] ?? '');
                    $caption = $blockData['caption'] ?? '';
                    if ($url) {
                        $htmlOutput .= "<figure class=\"my-6\"><img src=\"{$url}\" alt=\"{$caption}\" class=\"w-full max-h-[450px] object-cover rounded-2xl shadow-sm border border-gray-100\">";
                        if ($caption) {
                            $htmlOutput .= "<figcaption class=\"text-center text-xs text-gray-400 mt-2\">{$caption}</figcaption>";
                        }
                        $htmlOutput .= "</figure>";
                    }
                    break;

                case 'quote':
                    $text = $blockData['text'] ?? '';
                    $caption = $blockData['caption'] ?? '';
                    $htmlOutput .= "<blockquote class=\"border-l-4 border-emerald-500 pl-4 py-2 my-4 italic text-gray-700 bg-emerald-50/50 rounded-r-lg\"><p>\"{$text}\"</p>";
                    if ($caption) {
                        $htmlOutput .= "<cite class=\"block text-xs font-semibold text-emerald-800 mt-1 non-italic\">- {$caption}</cite>";
                    }
                    $htmlOutput .= "</blockquote>";
                    break;

                case 'warning':
                    $title = $blockData['title'] ?? '';
                    $message = $blockData['message'] ?? '';
                    $htmlOutput .= "<div class=\"p-4 my-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl text-amber-900\"><strong class=\"block font-bold text-sm mb-1\">{$title}</strong><p class=\"text-xs leading-relaxed\">{$message}</p></div>";
                    break;

                case 'delimiter':
                    $htmlOutput .= "<hr class=\"my-8 border-gray-200 border-t-2 border-dashed\">";
                    break;

                default:
                    // Fallback untuk block tipe teks biasa jika tidak teridentifikasi
                    if (isset($blockData['text'])) {
                        $htmlOutput .= "<p class=\"text-gray-600 leading-relaxed mb-4\">{$blockData['text']}</p>";
                    }
                    break;
            }
        }

        return $htmlOutput;
    }
}