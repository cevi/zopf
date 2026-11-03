/**
 * Text für den Einbau in HTML-Strings maskieren (Logbuch, Benachrichtigungen).
 * Werte aus Eingaben nie ungeprüft in .html() oder innerHTML einsetzen.
 */
export function escapeHtml(value) {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
