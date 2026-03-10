<?php
function renderBookCover($book, $size = 'normal') {
    $colors = ['cover-1','cover-2','cover-3','cover-4','cover-5','cover-6','cover-7','cover-8'];
    $colorClass = $colors[$book['id_buku'] % 8];
    $icons = ['bi-book', 'bi-journal-richtext', 'bi-book-half', 'bi-journals', 'bi-journal-text', 'bi-journal-bookmark'];
    $icon = $icons[$book['id_buku'] % 6];
    $heightClass = $size === 'large' ? 'height:280px' : 'height:200px';
    
    if (!empty($book['gambar_buku'])) {
        return '<img src="../assets/images/' . htmlspecialchars($book['gambar_buku']) . '" 
                     class="book-cover" style="' . $heightClass . '; object-fit:cover;" 
                     onerror="this.parentNode.innerHTML=\'<div class=\'book-cover-placeholder ' . $colorClass . '\' style=\'' . $heightClass . '\'><i class=\'bi ' . $icon . '\'></i></div>\'">';
    }
    return '<div class="book-cover-placeholder ' . $colorClass . '" style="' . $heightClass . '"><i class="bi ' . $icon . '"></i></div>';
}

function statusBadge($status) {
    $map = [
        'pending'  => ['warning', 'Menunggu'],
        'approved' => ['success', 'Disetujui'],
        'rejected' => ['danger',  'Ditolak'],
        'returned' => ['secondary','Dikembalikan'],
    ];
    $s = $map[$status] ?? ['dark', $status];
    return '<span class="badge bg-' . $s[0] . '">' . $s[1] . '</span>';
}
?>
