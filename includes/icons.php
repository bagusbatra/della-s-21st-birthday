<?php
/**
 * Ikon SVG modern (gaya Lucide, garis 2px sudut membulat) dipakai di
 * seluruh halaman PHP menggantikan emoji sebagai ikon UI/antarmuka.
 * Emoji di dalam KONTEN (isi pesan, surat cinta, dsb) TIDAK disentuh —
 * hanya emoji yang berfungsi sebagai ikon tombol/label/status.
 *
 * Sumber path: lucide-static (ISC License) — diambil apa adanya, tidak
 * digambar ulang dari ingatan supaya bentuknya akurat.
 */

function icon(string $name, string $class = 'icon'): string
{
    static $paths = [
        'arrow-up' => '<path d="m5 12 7-7 7 7"/> <path d="M12 19V5"/>',
        'cake' => '<path d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8"/> <path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1"/> <path d="M2 21h20"/> <path d="M7 8v3"/> <path d="M12 8v3"/> <path d="M17 8v3"/> <path d="M7 4h.01"/> <path d="M12 4h.01"/> <path d="M17 4h.01"/>',
        'calendar' => '<path d="M8 2v3"/> <path d="M16 2v3"/> <rect x="3" y="3" width="18" height="18" rx="2"/> <path d="M3 9h18"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'chevron-up' => '<path d="m18 15-6-6-6 6"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/> <path d="M12 6v6l4 2"/>',
        'copy' => '<rect width="14" height="14" x="8" y="8" rx="2" ry="2"/> <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>',
        'eye' => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/> <circle cx="12" cy="12" r="3"/>',
        'feather' => '<path d="M14.086 18.412A2 2 0 0112.67 19H5v-7.672a2 2 0 01.586-1.414L11.75 3.75a6 6 0 118.49 8.49z"/> <path d="M16 8 2 22"/> <path d="M17.488 15H9"/>',
        'flame' => '<path d="M12 3q1 4 4 6.5t3 5.5a1 1 0 0 1-14 0 5 5 0 0 1 1-3 1 1 0 0 0 5 0c0-2-1.5-3-1.5-5q0-2 2.5-4"/>',
        'heart' => '<path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/>',
        'home' => '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/> <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
        'hourglass' => '<path d="M5 22h14"/> <path d="M5 2h14"/> <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/> <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/>',
        'image' => '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/> <circle cx="9" cy="9" r="2"/> <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
        'lock' => '<rect width="18" height="11" x="3" y="11" rx="2" ry="2"/> <path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'mail' => '<path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/> <rect x="2" y="4" width="20" height="16" rx="2"/>',
        'map-pin' => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/> <circle cx="12" cy="10" r="3"/>',
        'menu' => '<path d="M4 5h16"/> <path d="M4 12h16"/> <path d="M4 19h16"/>',
        'message-circle' => '<path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/>',
        'music' => '<path d="M9 18V5l12-2v13"/> <circle cx="6" cy="18" r="3"/> <circle cx="18" cy="16" r="3"/>',
        'party-popper' => '<path d="M5.8 11.3 2 22l10.7-3.79"/> <path d="M4 3h.01"/> <path d="M22 8h.01"/> <path d="M15 2h.01"/> <path d="M22 20h.01"/> <path d="m22 2-2.24.75a2.9 2.9 0 0 0-1.96 3.12c.1.86-.57 1.63-1.45 1.63h-.38c-.86 0-1.6.6-1.76 1.44L14 10"/> <path d="m22 13-.82-.33c-.86-.34-1.82.2-1.98 1.11c-.11.7-.72 1.22-1.43 1.22H17"/> <path d="m11 2 .33.82c.34.86-.2 1.82-1.11 1.98C9.52 4.9 9 5.52 9 6.23V7"/> <path d="M11 13c1.93 1.93 2.83 4.17 2 5-.83.83-3.07-.07-5-2-1.93-1.93-2.83-4.17-2-5 .83-.83 3.07.07 5 2Z"/>',
        'pause' => '<rect x="14" y="3" width="5" height="18" rx="1"/> <rect x="5" y="3" width="5" height="18" rx="1"/>',
        'pencil' => '<path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/> <path d="m15 5 4 4"/>',
        'pen-line' => '<path d="M13 21h8"/> <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>',
        'play' => '<path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"/>',
        'plus' => '<path d="M5 12h14"/> <path d="M12 5v14"/>',
        'rotate-ccw' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/> <path d="M3 3v5h5"/>',
        'send' => '<path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/> <path d="m21.854 2.147-10.94 10.939"/>',
        'settings' => '<path d="M9.671 4.136a2.34 2.34 0 0 1 4.659 0 2.34 2.34 0 0 0 3.319 1.915 2.34 2.34 0 0 1 2.33 4.033 2.34 2.34 0 0 0 0 3.831 2.34 2.34 0 0 1-2.33 4.033 2.34 2.34 0 0 0-3.319 1.915 2.34 2.34 0 0 1-4.659 0 2.34 2.34 0 0 0-3.32-1.915 2.34 2.34 0 0 1-2.33-4.033 2.34 2.34 0 0 0 0-3.831A2.34 2.34 0 0 1 6.35 6.051a2.34 2.34 0 0 0 3.319-1.915"/> <circle cx="12" cy="12" r="3"/>',
        'share-2' => '<circle cx="18" cy="5" r="3"/> <circle cx="6" cy="12" r="3"/> <circle cx="18" cy="19" r="3"/> <line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/> <line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>',
        'skip-back' => '<path d="M17.971 4.285A2 2 0 0 1 21 6v12a2 2 0 0 1-3.029 1.715l-9.997-5.998a2 2 0 0 1-.003-3.432z"/> <path d="M3 20V4"/>',
        'skip-forward' => '<path d="M21 4v16"/> <path d="M6.029 4.285A2 2 0 0 0 3 6v12a2 2 0 0 0 3.029 1.715l9.997-5.998a2 2 0 0 0 .003-3.432z"/>',
        'sparkles' => '<path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/> <path d="M20 2v4"/> <path d="M22 4h-4"/> <circle cx="4" cy="20" r="2"/>',
        'trash-2' => '<path d="M10 11v6"/> <path d="M14 11v6"/> <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/> <path d="M3 6h18"/> <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
        'user' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/> <circle cx="12" cy="7" r="4"/>',
        'wind' => '<path d="M12.8 19.6A2 2 0 1 0 14 16H2"/> <path d="M17.5 8a2.5 2.5 0 1 1 2 4H2"/> <path d="M9.8 4.4A2 2 0 1 1 11 8H2"/>',
        'wrench' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"/>',
        'x' => '<path d="M18 6 6 18"/> <path d="m6 6 12 12"/>',
    ];

    if (!isset($paths[$name])) {
        return '';
    }

    return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="%s" aria-hidden="true">%s</svg>',
        htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
        $paths[$name]
    );
}
