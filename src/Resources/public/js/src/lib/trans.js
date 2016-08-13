/**
 * Translate message
 */
function trans(message) {
    if (typeof(translations[message]) != 'undefined') {
        return translations[message];
    } else {
        return message;
    }
}
