// Ausführen: node --test tests/js/
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { escapeHtml } from '../../resources/js/escape-html.js';

test('maskiert HTML-Sonderzeichen', () => {
    assert.equal(
        escapeHtml('<img src=x onerror="alert(1)"> & \'x\''),
        '&lt;img src=x onerror=&quot;alert(1)&quot;&gt; &amp; &#039;x&#039;'
    );
});

test('lässt normalen Text und Umlaute unverändert', () => {
    assert.equal(escapeHtml('3 Zöpfe wurden aufgeschnitten.'), '3 Zöpfe wurden aufgeschnitten.');
});

test('null und undefined werden zu leerem Text', () => {
    assert.equal(escapeHtml(null), '');
    assert.equal(escapeHtml(undefined), '');
});

test('Zahlen werden zu Text', () => {
    assert.equal(escapeHtml(5), '5');
});
