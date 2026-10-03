<?php

namespace App\Enums;

enum ProductKind: string
{
    case StickerPack = 'sticker_pack';
    case Video = 'video';
    case ColoringBook = 'coloring_book';
    case Ecard = 'ecard';

    public function label(): string
    {
        return match ($this) {
            self::StickerPack => 'Sticker Pack',
            self::Video => 'Custom Video',
            self::ColoringBook => 'Coloring Book',
            self::Ecard => 'eCard',
        };
    }

    public function isInstantDownload(): bool
    {
        return match ($this) {
            self::StickerPack, self::ColoringBook => true,
            self::Video, self::Ecard => false,
        };
    }

    public function ctaLabel(): string
    {
        return $this->isInstantDownload() ? 'Get the pack' : 'Make one';
    }
}
