<?php
/**
 * Inline SVG icon set (Tabler-style, hand-curated).
 * No external font files, no CDN — every icon is plain SVG.
 *
 * Usage:  echo SMS_Icons::svg( 'home', 20 );
 *
 * @package SchoolManagementStudio
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class SMS_Icons {

    public static function paths() {
        return array(
            'home'        => '<path d="M3 12 12 3l9 9"/><path d="M5 10v10h14V10"/>',
            'dashboard'   => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
            'school'      => '<path d="M3 21V9l9-6 9 6v12"/><path d="M9 21v-7h6v7"/>',
            'building'    => '<path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2"/><path d="M3 21h18"/>',
            'calendar'    => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>',
            'graduation'  => '<path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1 3 3 6 3s6-2 6-3v-5"/>',
            'users'       => '<circle cx="9" cy="9" r="3"/><circle cx="17" cy="9" r="2"/><path d="M3 19c0-3 3-5 6-5s6 2 6 5"/><path d="M15 19c0-2 2-3 4-3s4 1 4 3"/>',
            'user'        => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/>',
            'user-plus'   => '<circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-7 7-7s7 3 7 7"/><path d="M19 8v6M16 11h6"/>',
            'briefcase'   => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
            'shield'      => '<path d="M12 3 4 6v6c0 5 3 8 8 9 5-1 8-4 8-9V6z"/>',
            'badge'       => '<rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="9" cy="12" r="2.5"/><path d="M14 10h6M14 14h4"/>',
            'card'        => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/>',
            'award'       => '<circle cx="12" cy="9" r="6"/><path d="M9 14l-2 7 5-3 5 3-2-7"/>',
            'send'        => '<path d="M3 12 21 3l-9 18-2-7z"/>',
            'megaphone'   => '<path d="M3 11v3a1 1 0 0 0 1 1h2"/><path d="M14 21l-2-5"/><path d="M21 5 6 11v3l15 6z"/>',
            'book'        => '<path d="M3 6a3 3 0 0 1 3-3h12v18H6a3 3 0 0 1-3-3z"/><path d="M3 17h13"/>',
            'note'        => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6"/>',
            'task'        => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
            'clipboard'   => '<rect x="6" y="4" width="12" height="18" rx="2"/><rect x="9" y="2" width="6" height="4" rx="1"/>',
            'edit'        => '<path d="M4 20h4l11-11-4-4L4 16z"/><path d="M14 5l4 4"/>',
            'trash'       => '<path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M5 7l1 13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-13"/><path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/>',
            'plus'        => '<path d="M12 5v14M5 12h14"/>',
            'check'       => '<path d="M5 12l5 5L20 7"/>',
            'x'           => '<path d="M6 6l12 12M6 18 18 6"/>',
            'search'      => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.5-4.5"/>',
            'bell'        => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10 21a2 2 0 0 0 4 0"/>',
            'cog'         => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3 1.7 1.7 0 0 0 1-1.5V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8 1.7 1.7 0 0 0 1.5 1H21a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
            'money'       => '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="3"/>',
            'invoice'     => '<path d="M5 3h14a1 1 0 0 1 1 1v17l-3-2-3 2-3-2-3 2-3-2v-15a1 1 0 0 1 1-1z"/><path d="M9 8h6M9 12h6M9 16h4"/>',
            'chart-line'  => '<path d="M3 17 9 11l4 4 8-8"/><path d="M21 7v4M21 7h-4"/>',
            'chart-pie'   => '<path d="M12 3v9l9 0a9 9 0 0 0-9-9z"/><path d="M21 12a9 9 0 1 1-12-8.5"/>',
            'chart-bar'   => '<path d="M5 21V11"/><path d="M12 21V3"/><path d="M19 21v-7"/>',
            'truck'       => '<path d="M2 7h11v10H2z"/><path d="M13 11h5l3 3v3h-8"/><circle cx="6" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
            'route'       => '<circle cx="6" cy="6" r="2"/><circle cx="18" cy="18" r="2"/><path d="M8 6h6a4 4 0 0 1 0 8h-4a4 4 0 0 0 0 8h6"/>',
            'video'       => '<rect x="3" y="6" width="13" height="12" rx="2"/><path d="M16 10l5-3v10l-5-3z"/>',
            'ticket'      => '<path d="M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"/><path d="M9 7v10"/>',
            'printer'     => '<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="9" rx="2"/><path d="M6 14h12v8H6z"/>',
            'eye'         => '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>',
            'log-out'     => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
            'menu'        => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            'sun'         => '<circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4 7 17M17 7l1.4-1.4"/>',
            'moon'        => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>',
            'globe'       => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
            'cake'        => '<path d="M3 21h18v-7H3z"/><path d="M3 14V11a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v3"/><path d="M8 8V5M12 8V4M16 8V5"/>',
            'lock'        => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
            'mail'        => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
            'star'        => '<path d="m12 3 3 7 7 1-5 5 1 7-6-3-6 3 1-7-5-5 7-1z"/>',
            'list'        => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
            'grid'        => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
            'arrow-right' => '<path d="M5 12h14M13 5l7 7-7 7"/>',
            'arrow-left'  => '<path d="M19 12H5M11 5l-7 7 7 7"/>',
            'tag'         => '<path d="M20 12 12 20a2 2 0 0 1-3 0L3 14a2 2 0 0 1 0-3l8-8h7v7z"/><circle cx="15" cy="9" r="1.2"/>',
            'flag'        => '<path d="M5 21V4"/><path d="M5 4h12l-2 4 2 4H5"/>',
            'category'    => '<path d="M12 3 3 8l9 5 9-5-9-5z"/><path d="M3 14l9 5 9-5"/>',
            'switch-h'    => '<path d="M5 9h14M16 6l3 3-3 3M19 15H5M8 12l-3 3 3 3"/>',
            'download'    => '<path d="M12 3v12"/><path d="M7 11l5 5 5-5"/><path d="M5 21h14"/>',
            'upload'      => '<path d="M12 21V9"/><path d="M7 13l5-5 5 5"/><path d="M5 3h14"/>',
        );
    }

    /**
     * Render an SVG.
     */
    public static function svg( $name, $size = 20, $stroke = 1.6 ) {
        $paths = self::paths();
        if ( ! isset( $paths[ $name ] ) ) $name = 'grid';
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' . esc_attr( $stroke ) . '" stroke-linecap="round" stroke-linejoin="round" class="sms-icon sms-icon-' . esc_attr( $name ) . '">' . $paths[ $name ] . '</svg>';
    }
}
