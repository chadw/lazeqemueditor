import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';
import TomSelect from "tom-select";
import "@melloware/coloris/dist/coloris.css";
import Coloris from "@melloware/coloris";
import { validRaceModels } from './race-models';
window.validRaceModels = validRaceModels;

Alpine.plugin(intersect)
Alpine.plugin(collapse)

const baseUrl = document.querySelector('base')?.getAttribute('href') || '/';

Alpine.data('durationHelper', () => ({
    seconds: 0,

    human() {
        let s = Math.floor(Number(this.seconds) || 0);

        const days = Math.floor(s / 86400);
        s %= 86400;
        const hours = Math.floor(s / 3600);
        s %= 3600;
        const minutes = Math.floor(s / 60);
        const seconds = s % 60;

        const parts = [];
        if (days) parts.push(`${days}d`);
        if (hours) parts.push(`${hours}h`);
        if (minutes) parts.push(`${minutes}m`);
        if (seconds) parts.push(`${seconds}s`);
        if (parts.length === 0) return '0s';

        return parts.join(' ');
    },

    init() {
        const input = this.$el.querySelector('input');
        const root = this.$el.querySelector('.form-control');
        if (!input || !root) return;

        const apply = () => {
            root.setAttribute('label-suffix', this.human() ? ` (${this.human()})` : '');
            root.dispatchEvent(new CustomEvent('label-suffix-changed', { bubbles: true }));
        };

        this.$nextTick(() => {
            let attempts = 0;
            const readInput = () => {
                const fieldName = input.name || input.getAttribute('name');
                let val = Number(input.value) || 0;
                if (this.$store && this.$store.modalForm && fieldName) {
                    const storeVal = Number(this.$store.modalForm.form[fieldName] ?? 0) || 0;
                    if (storeVal !== 0) val = storeVal;
                }

                if (val !== this.seconds) {
                    this.seconds = val;
                    apply();
                }

                if (this.seconds === 0 && attempts < 5) {
                    attempts++;
                    setTimeout(readInput, 50);
                }
            };
            readInput();
        });

        if (this.$store && this.$store.modalForm) {
            this.$watch('$store.modalForm.isOpen', (open) => {
                if (open) {
                    this.$nextTick(() => {
                        this.seconds = Number(input.value) || 0;
                        apply();
                    });
                }
            });
        }

        this.$watch('seconds', (val) => {
            const num = Number(val) || 0;
            const fieldName = input.name || input.getAttribute('name');
            if (this.$store && this.$store.modalForm && fieldName) {
                this.$store.modalForm.form[fieldName] = num;
            }
            input.value = num;
            apply();
        });

        input.addEventListener('input', () => {
            this.seconds = Number(input.value) || 0;
        });
    },
}));

Alpine.data('aaSpellEffects', (opts = {}) => ({
    spaDefs: null,
    init() {
        try {
            const raw = this.$el.getAttribute && this.$el.getAttribute('data-spa-defs');
            if (raw) {
                try { this.spaDefs = JSON.parse(raw); } catch (e) { this.spaDefs = raw; }
            } else if (window.__EQEMU_SPA_DEFS) {
                this.spaDefs = window.__EQEMU_SPA_DEFS;
            } else if (window.eqemu_spa_defs) {
                this.spaDefs = window.eqemu_spa_defs;
            } else {
                this.spaDefs = window.spaDefs || null;
            }
        } catch (e) {
            this.spaDefs = null;
        }

        this.$el.addEventListener('change', (ev) => {
            const sel = ev.target;
            if (!sel || sel.tagName !== 'SELECT') return;
            if (sel.getAttribute('name') !== 'effect_id[]' && sel.getAttribute('name') !== 'effect_id') return;
            this.onEffectChanged(sel);
        });

        const existing = Array.from(this.$el.querySelectorAll('select[name="effect_id[]"]'));
        const prefillMap = new Map();
        const addToPrefill = (searchUrl, id) => {
            if (!id) return;
            const key = String(id);
            if (!prefillMap.has(searchUrl)) prefillMap.set(searchUrl, new Set());
            prefillMap.get(searchUrl).add(key);
        };

        existing.forEach(sel => {
            try {
                const val = sel.value ? String(sel.value).trim() : '';
                const id = Number(val) || 0;
                const def = (this.spaDefs && this.spaDefs[id]) ? this.spaDefs[id] : null;
                const row = sel.closest('tr') || sel.closest('div') || this.$el;
                if (!row) return;

                const baseType = def ? (def.base || '') : '';
                const limitType = def ? (def.limit || '') : '';

                const wantsSpellBase = baseType.toString().toLowerCase().includes('spell');
                const wantsSpellLimit = limitType.toString().toLowerCase().includes('spell');

                if (wantsSpellBase) {
                    const existingBase = row.querySelector('[name="base1[]"]');
                    const raw = existingBase ? (existingBase.value || '') : '';

                    if (raw === '' || raw === '0' || raw === '-1') {
                        // skip
                    } else {
                        const idVal = String(raw).startsWith('-') ? String(raw).slice(1) : String(raw);
                        if (idVal) addToPrefill('/spells/search', idVal);
                    }
                }
                if (wantsSpellLimit) {
                    const existingLimit = row.querySelector('[name="base2[]"]');
                    const raw = existingLimit ? (existingLimit.value || '') : '';

                    if (raw === '' || raw === '0' || raw === '-1') {
                        // skip
                    } else {
                        const idVal = String(raw).startsWith('-') ? String(raw).slice(1) : String(raw);
                        if (idVal) addToPrefill('/spells/search', idVal);
                    }
                }
            } catch (e) {
            }
        });

        window.__eq_prefill_cache = window.__eq_prefill_cache || new Map();
        const batchPromises = [];
        for (const [searchUrl, idSet] of prefillMap.entries()) {
            const ids = Array.from(idSet).filter(Boolean);
            if (!ids.length) continue;
            const cacheKeyPrefix = `${searchUrl}|`;

            // batch request ids=1,2,3
            const batchUrl = `${searchUrl}?ids=${ids.join(',')}`;
            const p = fetch(batchUrl)
                .then(async (res) => {
                    if (!res.ok) throw new Error('batch-failed');
                    const data = await res.json();
                    if (Array.isArray(data)) {
                        window.__eq_prefill_store = window.__eq_prefill_store || new Map();
                        data.forEach(item => {
                            const idStr = String(item.id ?? item.ID ?? '');
                            const cacheKey = cacheKeyPrefix + idStr;

                            window.__eq_prefill_cache.set(cacheKey, Promise.resolve(item));
                            window.__eq_prefill_store.set(cacheKey, item);
                        });
                    }
                }).catch(() => {
                    ids.forEach(id => {
                        const cacheKey = cacheKeyPrefix + id;
                        if (!window.__eq_prefill_cache.has(cacheKey)) {
                            const q = fetch(`${searchUrl}?q=${encodeURIComponent(id)}`)
                                .then(r => r.ok ? r.json().then(d => Array.isArray(d) && d.length ? d[0] : null) : null)
                                .catch(() => null);
                            window.__eq_prefill_cache.set(cacheKey, q);
                        }
                    });
                });

            batchPromises.push(p);
        }

        Promise.all(batchPromises).finally(() => {
            existing.forEach(s => this.onEffectChanged(s));
        });

        try {
            const mo = new MutationObserver((records) => {
                for (const r of records) {
                    for (const n of Array.from(r.addedNodes || [])) {
                        if (!(n instanceof HTMLElement)) continue;
                        const selects = Array.from(n.querySelectorAll ? n.querySelectorAll('select[name="effect_id[]"]') : []);

                        if (n.matches && n.matches('select[name="effect_id[]"]')) selects.unshift(n);
                        selects.forEach(s => this.onEffectChanged(s));
                    }
                }
            });
            mo.observe(this.$el, { childList: true, subtree: true });
        } catch (e) {
        }
    },

    onEffectChanged(selectEl) {
        try {
            const val = selectEl.value ? String(selectEl.value).trim() : '';
            const id = Number(val) || 0;
            const def = (this.spaDefs && this.spaDefs[id]) ? this.spaDefs[id] : null;

            const row = selectEl.closest('tr') || selectEl.closest('div') || this.$el;
            if (!row) return;

            this._ensureFieldControl(row, 'base1', def ? def.base : null, id);
            this._ensureFieldControl(row, 'base2', def ? def.limit : null, id);
        } catch (e) {
        }
    },

    _ensureFieldControl(row, fieldName, spaTypeRaw, effectId) {
        try {
            const inputName = `${fieldName}[]`;
            const existing = row.querySelector(`[name="${inputName}"]`);
            const spaType = (spaTypeRaw || '').toString().toLowerCase();
            const wantsSpell = spaType.includes('spellid') || spaType.includes('spell id') || spaType.includes('spell');
            const wantsItem = spaType.includes('item') || spaType.includes('itemid') || spaType.includes('item id');

            if (wantsSpell || wantsItem) {
                const alreadyContainer = existing && typeof existing.closest === 'function' ? existing.closest('[data-aarank-enhanced]') : null;
                if (alreadyContainer && alreadyContainer.dataset && alreadyContainer.dataset.aarankEnhanced === '1') return;

                const currentRaw = existing ? (existing.value ?? '') : '';
                const isNegative = String(currentRaw).startsWith('-');
                const currentId = isNegative ? String(currentRaw).slice(1) : String(currentRaw || '');
                const container = document.createElement('div');
                container.className = 'flex items-center gap-2';

                const selectWrapper = document.createElement('div');
                selectWrapper.className = 'flex-1';

                const visSel = document.createElement('select');
                visSel.className = 'w-full';
                visSel.dataset.searchUrl = wantsSpell ? '/spells/search' : '/items/search';
                if (currentId) {
                    const o = document.createElement('option'); o.value = currentId; o.textContent = currentId; visSel.appendChild(o);
                }
                selectWrapper.appendChild(visSel);

                const modeSel = document.createElement('select');
                modeSel.className = 'select w-20';
                const optAuto = document.createElement('option'); optAuto.value = 'auto'; optAuto.text = 'Auto';
                const optExclude = document.createElement('option'); optExclude.value = 'exclude'; optExclude.text = 'Exclude';
                modeSel.appendChild(optAuto); modeSel.appendChild(optExclude);
                if (isNegative) modeSel.value = 'exclude'; else modeSel.value = 'auto';

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = inputName;
                hidden.value = currentRaw;

                container.appendChild(selectWrapper);
                container.appendChild(modeSel);
                container.appendChild(hidden);

                if (existing) {
                    existing.parentNode.replaceChild(container, existing);
                } else {
                    const td = row.querySelector('td') || row;
                    td.appendChild(container);
                }

                // mark as enhanced
                container.dataset.aarankEnhanced = '1';
                if (window.TomSelect) {
                    try {
                        const searchUrl = wantsSpell ? '/spells/search' : '/items/search';

                        window.__eq_prefill_cache = window.__eq_prefill_cache || new Map();
                        if (currentRaw === '' || currentRaw === '0' || currentRaw === '-1') {
                            // leave the placeholder option as-is and don't prefill
                        } else if (currentId) {
                            try {
                                const cacheKey = `${searchUrl}|${currentId}`;
                                const syncItem = window.__eq_prefill_store && window.__eq_prefill_store.get(cacheKey);

                                if (syncItem) {
                                    const opt = visSel.querySelector('option');
                                    if (opt) opt.textContent = `${syncItem.id ?? syncItem.ID ?? currentId}: ${syncItem.name ?? syncItem.Name ?? syncItem.title ?? syncItem.label ?? ''}`;
                                }

                                let p = window.__eq_prefill_cache.get(cacheKey);
                                if (!p) {
                                    p = (async () => {
                                        try {
                                            const res = await fetch(`${searchUrl}?q=${encodeURIComponent(currentId)}`);
                                            if (!res.ok) return null;
                                            const data = await res.json();
                                            return Array.isArray(data) && data.length ? data[0] : null;
                                        } catch (e) { return null; }
                                    })();
                                    window.__eq_prefill_cache.set(cacheKey, p);
                                }
                                p.then(item => {
                                    if (!item) return;
                                    const opt = visSel.querySelector('option');
                                    if (opt) opt.textContent = `${item.id ?? item.ID ?? currentId}: ${item.name ?? item.Name ?? item.title ?? item.label ?? ''}`;
                                });
                            } catch (e) {
                            }
                        }

                        const ts = new TomSelect(visSel, {
                            valueField: 'id',
                            labelField: 'name',
                            searchField: ['name', 'id'],
                            preload: false,
                            maxItems: 1,
                            create: false,
                            load: async (query, callback) => {
                                if (!query.length) return callback();
                                try {
                                    const res = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`);
                                    if (!res.ok) return callback();
                                    const data = await res.json();
                                    callback(data.map(item => ({ id: String(item.id ?? item.ID ?? ''), name: `${item.id ?? item.ID}: ${item.name ?? item.Name ?? item.title ?? item.label ?? ''}` })));
                                } catch (e) { callback(); }
                            }
                        });

                        visSel.__ts_inst = ts;

                        if (currentId) {
                            try {
                                const cacheKey = `${searchUrl}|${currentId}`;
                                const p = window.__eq_prefill_cache.get(cacheKey);
                                if (p && p.then) {
                                    p.then(item => {
                                        if (item) {
                                            const idStr = String(item.id ?? item.ID ?? currentId);
                                            const label = item.name ?? item.Name ?? item.title ?? item.label ?? '';
                                            try { ts.addOption({ id: idStr, name: `${idStr}: ${label}`, new_icon: item.new_icon ?? null }); } catch (e) { }
                                            try { ts.setValue(idStr, true); } catch (e) { }
                                        } else {
                                            try { ts.setValue(currentId, true); } catch (e) { }
                                        }
                                    }).catch(() => { try { ts.setValue(currentId, true); } catch (e) { } });
                                } else if (p) {
                                    const item = p;
                                    const idStr = String(item.id ?? item.ID ?? currentId);
                                    const label = item.name ?? item.Name ?? item.title ?? item.label ?? '';
                                    try { ts.addOption({ id: idStr, name: `${idStr}: ${label}`, new_icon: item.new_icon ?? null }); } catch (e) { }
                                    try { ts.setValue(idStr, true); } catch (e) { }
                                } else {
                                    try { ts.setValue(currentId, true); } catch (e) { }
                                }
                            } catch (e) { try { ts.setValue(currentId, true); } catch (e) { } }
                        }

                        // helper to sync hidden input from ts+mode
                        const syncHidden = () => {
                            const val = (ts.getValue && ts.getValue()) || '';
                            if (!val) { hidden.value = ''; return; }
                            hidden.value = modeSel.value === 'exclude' ? `-${val}` : `${val}`;
                        };

                        // on change of tomselect, update hidden
                        ts.on && ts.on('change', syncHidden);

                        // on change of mode, update hidden (and keep ts value)
                        modeSel.addEventListener('change', () => {
                            syncHidden();
                        });

                    } catch (e) {
                    }
                }

                return;
            }

            const curVal = existing && existing.tagName === 'SELECT' && existing.__ts_inst ? (existing.__ts_inst.getValue && existing.__ts_inst.getValue()) || '' : (existing ? existing.value : '');
            const inp = document.createElement('input');
            inp.type = 'text';
            inp.name = inputName;
            inp.className = 'input w-full';
            inp.value = curVal || '';

            const enhancedContainer = existing && typeof existing.closest === 'function' ? existing.closest('[data-aarank-enhanced]') : null;
            if (enhancedContainer) {
                try {
                    const vis = enhancedContainer.querySelector && enhancedContainer.querySelector('select');
                    if (vis && vis.__ts_inst && typeof vis.__ts_inst.destroy === 'function') {
                        try { vis.__ts_inst.destroy(); } catch (e) { }
                    }
                } catch (e) {
                }
                enhancedContainer.parentNode.replaceChild(inp, enhancedContainer);
            } else if (existing) {
                // if select had TomSelect, destroy it
                if (existing.__ts_inst && typeof existing.__ts_inst.destroy === 'function') {
                    try { existing.__ts_inst.destroy(); } catch (e) { }
                }
                existing.parentNode.replaceChild(inp, existing);
            } else {
                const td = row.querySelector('td') || row;
                td.appendChild(inp);
            }
        } catch (e) {
        }
    }
}));

Alpine.data('numberHelper', (decimals = 0) => ({
    value: '',
    decimals: Number(decimals) || 0,

    formatted() {
        const s = this.value === null || this.value === undefined ? '' : String(this.value).trim();
        if (s === '') return '';

        const raw = s.replace(/,/g, '');
        const num = Number(raw);
        if (Number.isNaN(num)) return s;

        if (this.decimals > 0) {
            return num.toLocaleString(undefined, { minimumFractionDigits: this.decimals, maximumFractionDigits: this.decimals });
        }

        if (raw.includes('.')) {
            const parts = raw.split('.');
            const intNum = Number(parts[0]) || 0;
            const intFormatted = intNum.toLocaleString();
            return intFormatted + '.' + parts[1];
        }

        return Math.round(num).toLocaleString();
    },

    init() {
        const input = this.$el.querySelector('input');
        const root = this.$el.querySelector('.form-control');
        if (!input || !root) return;

        this.value = input.value || '';

        const apply = () => {
            const fmt = this.formatted();
            root.setAttribute('label-suffix', fmt ? ` (${fmt})` : '');
            root.dispatchEvent(new CustomEvent('label-suffix-changed', { bubbles: true }));
        };

        apply();

        if (this.$store && this.$store.modalForm) {
            this.$watch('$store.modalForm.isOpen', (open) => {
                if (open) {
                    this.value = input.value || '';
                }
            });
        }

        this.$watch('value', apply);

        input.addEventListener('input', () => {
            this.value = input.value || '';
        });
    }
}));

Alpine.data('currencyHelper', (initial = 0) => ({
    amount: Number(initial) || 0,

    formatted() {
        let p = Math.floor(this.amount / 1000);
        let remainder = this.amount % 1000;

        let g = Math.floor(remainder / 100);
        remainder %= 100;

        let s = Math.floor(remainder / 10);
        let c = remainder % 10;

        const parts = [];
        if (p > 0) parts.push(`${p} pp`);
        if (g > 0) parts.push(`${g} gp`);
        if (s > 0) parts.push(`${s} sp`);
        if (c > 0 || parts.length === 0) parts.push(`${c} cp`);

        return parts.join(' ');
    },

    init() {
        const root = this.$el.querySelector('.form-control');
        if (!root) return;

        const apply = () => {
            const text = this.formatted();
            root.setAttribute('label-suffix', text ? ` (${text})` : '');
            root.dispatchEvent(new CustomEvent('label-suffix-changed', { bubbles: true }));
        };

        apply();
        this.$watch('amount', apply);
    },
}));

Alpine.data('formTracker', () => ({
    initialStates: new Map(),
    dirtyFields: new Set(),

    init() {
        this.$watch('$store.modalForm.isOpen', (val) => {
            this.resetTracker();
        });
        this.setupBaseline();
    },

    setupBaseline() {
        setTimeout(() => {
            const inputs = this.$el.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                if (input.classList.contains('tab')) return;

                const isCheckable = ['checkbox', 'radio'].includes(input.type);
                const val = isCheckable ? Boolean(input.checked) : input.value;
                this.initialStates.set(input, val);

                if (!input.dataset.tracked) {
                    ['input', 'change'].forEach(event => {
                        // Explicitly bind 'this' to the component instance
                        input.addEventListener(event, (e) => {
                            this.updateField(e.target, isCheckable);
                        });
                    });
                    input.dataset.tracked = "true";
                }
            });
        }, 300);
    },

    updateField(el, isCheckable) {
        if (!el || !this.initialStates.has(el)) return;

        const currentVal = isCheckable ? Boolean(el.checked) : el.value;
        const initialVal = isCheckable ? Boolean(this.initialStates.get(el)) : this.initialStates.get(el);
        const isDirty = currentVal !== initialVal;

        isDirty ? this.dirtyFields.add(el) : this.dirtyFields.delete(el);

        const container = el.closest('[data-ability]') || el.closest('.form-control') || el.closest('label');
        const labelText = container ? container.querySelector('.label-text') : null;

        if (isDirty) {
            el.classList.add('border-warning', 'ring-2', 'ring-warning/20', 'shadow-[0_0_8px_rgba(0,184,211,0.4)]');
            if (labelText) labelText.classList.add('text-warning', 'font-bold');
        } else {
            el.classList.remove('border-warning', 'ring-2', 'ring-warning/20', 'shadow-[0_0_8px_rgba(0,184,211,0.4)]');
            if (labelText) labelText.classList.remove('text-warning', 'font-bold');
        }

        // make tab button dirty if any field within its content is dirty
        const tabContent = el.closest('.tab-content');
        if (tabContent) {
            const tabButton = tabContent.previousElementSibling;
            if (tabButton && tabButton.classList.contains('tab')) {
                const anyDirtyInTab = Array.from(this.dirtyFields).some(field =>
                    field.closest('.tab-content') === tabContent
                );

                anyDirtyInTab ? tabButton.classList.add('tab-dirty') : tabButton.classList.remove('tab-dirty');
            }
        }
    },

    resetTracker() {
        this.dirtyFields.forEach(el => {
            el.classList.remove('border-warning', 'ring-2', 'ring-warning/20', 'shadow-[0_0_8px_rgba(0,184,211,0.4)]');
            const container = el.closest('[data-ability]') || el.closest('.form-control') || el.closest('label');
            const labelText = container ? container.querySelector('.label-text') : null;
            if (labelText) labelText.classList.remove('text-warning', 'font-bold');
        });

        this.initialStates.clear();
        this.dirtyFields.clear();
        this.setupBaseline();
    },

    get isDirty() { return this.dirtyFields.size > 0; }
}));

Alpine.data('spellEffects', () => {
    return {
        activeEffects: [],
        spellValues: {},
        teleport_zone: '',
        selectedIndex: null,
        showModal: false,
        modalType: null, // zones, pets, horses, auras
        modalIndex: null,
        modalFilter: '',
        teleports: [83, 88, 104, 145],
        pets: [33, 67, 71, 106, 108, 152, 167],
        horses: [113],
        auras: [351],
        dbRaces: {},
        spaDefs: {},
        spellValues: {},

        init() {
            try {
                const ia = this.$el?.dataset?.initialActive;
                const sv = this.$el?.dataset?.spellValues;
                const tv = this.$el?.dataset?.teleport ?? null;
                const dr = this.$el?.dataset?.dbRaces;

                const initialActive = ia ? JSON.parse(ia) : [];
                const spellValues = sv ? JSON.parse(sv) : {};
                const active = Array.isArray(initialActive) ? initialActive.map(Number) : [];
                const values = {};
                Object.keys(spellValues || {}).forEach(k => {
                    values[Number(k)] = spellValues[k];
                });

                for (let i = 1; i <= 12; i++) {
                    values[i] = Object.assign({ teleport_zone: '' }, values[i] || {});
                }

                if (dr) {
                    try { this.dbRaces = JSON.parse(dr); } catch (e) { console.error('dbRaces parse error', e); this.dbRaces = {}; }
                } else {
                    this.dbRaces = {};
                }

                let parsedTv = tv;
                if (typeof tv === 'string' && tv.length) {
                    try {
                        parsedTv = JSON.parse(tv);
                    } catch (e) {
                        if ((tv.startsWith('"') && tv.endsWith('"')) || (tv.startsWith("'") && tv.endsWith("'"))) {
                            parsedTv = tv.slice(1, -1);
                        } else {
                            parsedTv = tv;
                        }
                    }
                }

                this.spaDefs = JSON.parse(this.$el.dataset.spaDefs);
                this.spellValues = values;

                // Merge provided initial active indices with any slots that
                // already contain an effect id in `spellValues`. This ensures
                // that secondary effects (e.g. coordinates for Teleport)
                // are shown without requiring the user to toggle them.
                const activeSet = new Set((Array.isArray(active) ? active : []).map(Number));
                for (let i = 1; i <= 12; i++) {
                    const sv = this.spellValues[i] || {};
                    const eid = Number(sv.effectid) || 0;
                    const base = Number(sv.effect_base_value) || 0;
                    const limit = Number(sv.effect_limit_value) || 0;
                    const max = Number(sv.max) || 0;

                    const hasValues = base !== 0 || limit !== 0 || max !== 0;

                    // Include if:
                    // - effect id is set and not the 'blank' SPA (254), or
                    // - effect id is 254 but there are non-zero values, or
                    // - there are non-zero base/limit/max values even if effectid is falsy
                    if (eid > 0) {
                        if (eid !== 254 || hasValues) activeSet.add(i);
                    } else if (hasValues) {
                        activeSet.add(i);
                    }
                }
                this.activeEffects = Array.from(activeSet).sort((a, b) => a - b);

                this.teleport_zone = parsedTv ?? '';
                this.selectedIndex = this.activeEffects.length ? this.activeEffects[0] : null;

                // If global teleport_zone has a value, and individual slots do not,
                // prefill slots that are of special types (zones/pets/horses/auras)
                if (this.teleport_zone) {
                    for (let i = 1; i <= 12; i++) {
                        const eid = Number(this.spellValues[i]?.effectid) || 0;
                        if (!eid) continue;
                        if ((this.teleports.includes(eid) || this.pets.includes(eid) || this.horses.includes(eid) || this.auras.includes(eid)) && !this.spellValues[i].teleport_zone) {
                            this.spellValues[i].teleport_zone = this.teleport_zone;
                        }
                    }
                }

                // after child components (ajaxSelect) initialize, populate typed selects
                this.$nextTick(() => {
                    for (let i = 1; i <= 12; i++) {
                        // if slot already has an effect id, watch for changes
                        if (this.spellValues[i] && Number(this.spellValues[i].effectid)) {
                            this.watchEffectId(i);
                        }

                        // populate any typed selects (item id / spell id) present for base/limit/max
                        this.populateTypedField(i, 'base').catch(() => { });
                        this.populateTypedField(i, 'limit').catch(() => { });
                        this.populateTypedField(i, 'max').catch(() => { });
                    }
                });
            } catch (e) {
                console.error('spellEffects init parse error', e);
            }
        },
        spaFieldType(i, field) {
            const spaId = Number(this.spellValues[i]?.effectid);
            const raw = this.spaDefs?.[spaId]?.[field];

            if (!raw) return 'number';

            return raw.toString().trim().toLowerCase();
        },
        isItemField(i, field) {
            return this.spaFieldType(i, field).includes('item');
        },

        isSpellField(i, field) {
            // Only treat exact 'spellid' (allowing optional spaces) as a spell field.
            const t = this.spaFieldType(i, field);
            if (!t) return false;
            const compact = t.replace(/\s+/g, '');
            return compact === 'spellid';
        },
        isSpellEffectField(i, field) {
            // fields that represent a spell effect id variants
            const t = this.spaFieldType(i, field);
            if (!t) return false;
            const compact = t.replace(/\s+/g, '').toLowerCase();
            return compact === 'spelleffect' || compact === 'spelleffectid' || compact === 'spell_effect';
        },
        isRaceField(i, field) {
            // Only treat specific SPA IDs as race fields
            const spaId = Number(this.spellValues[i]?.effectid) || 0;
            const raceSpas = new Set([58, 412]);
            // race is used in the `base` position for these SPAs
            return field === 'base' && raceSpas.has(spaId);
        },
        isLimitSpellTypeField(i, field) {
            // Show a 0/1 (Detrimental/Beneficial) selector for certain SPA types.
            // Only applies to the `base` column when the effect SPA id is 138.
            if (field !== 'base') return false;
            const val = Number(this.spellValues?.[i]?.effectid || 0);
            return val === 138;
        },
        isLimitSpellField(i, field) {
            // SPA 139 (SE_LimitSpell) should render a spell selector in `base`
            if (field !== 'base') return false;
            const val = Number(this.spellValues?.[i]?.effectid || 0);
            return val === 139;
        },
        sortedActive() { return this.activeEffects.slice().sort((a, b) => a - b); },
        toggleEffect(i) {
            i = Number(i);

            // if already active, remove it
            if (this.activeEffects.includes(i)) {
                this.activeEffects = this.activeEffects.filter(e => e !== i);
                // adjust selectedIndex if needed
                if (this.selectedIndex === i) {
                    this.selectedIndex = this.activeEffects.length ? this.activeEffects[0] : null;
                }
                // mark the slot as blank so server can clear it on save
                try {
                    if (!this.spellValues) this.spellValues = {};
                    this.spellValues[i] = {
                        effectid: 254,
                        effect_base_value: 0,
                        effect_limit_value: 0,
                        max: 0,
                        formula: 0,
                        teleport_zone: '',
                    };
                } catch (e) {}
                return;
            }

            if (!this.spellValues[i]) {
                this.spellValues[i] = {
                    effectid: 0,
                    effect_base_value: 0,
                    effect_limit_value: 0,
                    max: 0,
                    formula: 0,
                };
            }

            this.$nextTick(() => this.watchEffectId(i));

            this.activeEffects = [...this.activeEffects, i];
        },
        async onEffectIdChanged(i) {
            i = Number(i);

            const spaId = Number(this.spellValues[i]?.effectid);
            if (!spaId) return;

            // Load example values from backend
            try {
                const res = await fetch(`/spells/defaults/${spaId}`);
                if (!res.ok) return;

                const data = await res.json();
                if (!data) return;

                // Populate numeric defaults
                this.spellValues[i].effect_base_value = data.base ?? 0;
                this.spellValues[i].effect_limit_value = data.limit ?? 0;
                this.spellValues[i].max = data.max ?? 0;
                this.spellValues[i].formula = data.formula ?? 0;

                // Populate typed fields (item / spell)
                this.populateTypedDefaults(i, 'base');
                this.populateTypedDefaults(i, 'limit');
                this.populateTypedDefaults(i, 'max');

            } catch (e) {
                console.error('SPA lookup failed', e);
            }
        },
        setSelected(i) {
            this.selectedIndex = Number(i);

            // does slot need a teleport_zone selector?
            try {
                const idx = Number(this.selectedIndex);
                if (idx && this.teleport_zone && !this.spellValues[idx].teleport_zone) {
                    this.spellValues[idx].teleport_zone = this.teleport_zone;
                }
            } catch (e) {
            }
        },
        isSpecial(i) {
            if (!i) return false;
            const id = Number(this.spellValues[i]?.effectid);
            return this.teleports.includes(id) || this.pets.includes(id) || this.horses.includes(id) || this.auras.includes(id);
        },
        getModalTypeForEffectId(id) {
            id = Number(id);
            if (this.teleports.includes(id)) return 'zones';
            if (this.pets.includes(id)) return 'pets';
            if (this.horses.includes(id)) return 'horses';
            if (this.auras.includes(id)) return 'auras';
            return null;
        },
        getModalItems() {
            const type = this.modalType;
            if (!type) return [];
            const store = Alpine.store('modalCache');
            const arr = Array.isArray(store[type]) ? store[type] : [];
            const raw = (this.modalFilter ?? '') + '';
            const filterRaw = raw.trim();
            if (!filterRaw) return arr;

            const normalize = s => (s || '').toString()
                .replace(/[\u00A0-\u9999<>\&]/g, ' ')
                .replace(/[^\w\s]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();

            const normFilter = normalize(filterRaw);
            const escapeRegExp = (str) => str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

            // field lists per modal type
            const fieldMap = {
                zones: ['long_name', 'short_name', 'zoneidnumber'],
                pets: ['type', 'npcID'],
                horses: ['filename', 'race', 'gender', 'texture', 'mountspeed', 'notes'],
                auras: ['name', 'aura_type', 'distance']
            };
            const fields = fieldMap[type] || ['name', 'short_name', 'long_name', 'id'];

            return arr.filter(it => {
                const haySrc = fields.map(f => (it[f] ?? '')).join(' ').trim();
                const hay = normalize(haySrc);

                if (hay.includes(normFilter)) return true;

                const tokens = normFilter.split(/\s+/).filter(Boolean);
                return tokens.every(tok => {
                    const re = new RegExp('\\b' + escapeRegExp(tok), 'i');
                    return re.test(hay);
                });
            });
        },

        openSelector(i, type) {
            this.modalIndex = i;
            this.modalType = type;
            this.modalFilter = '';
            Alpine.store('modalCache').fetch(type).catch(() => { });
            this.showModal = true;
        },

        openSelectorForSelected() {
            let i = this.selectedIndex;

            if (!i && this.activeEffects && this.activeEffects.length) {
                i = this.activeEffects[0];
            }

            if (!i) {
                for (const idx of (this.activeEffects || [])) {
                    const id = Number(this.spellValues[idx]?.effectid);
                    if (this.teleports.includes(id) || this.pets.includes(id) || this.horses.includes(id) || this.auras.includes(id)) {
                        i = idx;
                        break;
                    }
                }
            }

            if (!i) {
                return;
            }

            this.selectedIndex = Number(i);
            const id = Number(this.spellValues[this.selectedIndex]?.effectid);
            const type = this.getModalTypeForEffectId(id);
            if (!type) {
                return;
            }
            this.openSelector(this.selectedIndex, type);
        },

        closeModal() {
            this.showModal = false;
            this.modalType = null;
            this.modalIndex = null;
            this.modalFilter = '';
        },

        selectItem(item) {
            let val = '';
            try {
                const type = this.modalType;
                if (!item) {
                    val = '';
                } else {
                    switch (type) {
                        case 'zones':
                            val = item?.short_name ?? '';
                            break;
                        case 'pets':
                            val = item?.type ?? '';
                            break;
                        case 'horses':
                            val = item?.filename ?? item?.name ?? item?.id ?? '';
                            break;
                        case 'auras':
                            val = item?.name ?? item?.label ?? item?.id ?? '';
                            break;
                        default:
                            val = '';
                    }
                }
            } catch (e) {
                val = item?.short_name ?? item?.name ?? item?.id ?? '';
            }

            if (this.modalIndex !== null && this.modalIndex !== undefined) {
                const idx = Number(this.modalIndex);
                if (!this.spellValues[idx]) this.spellValues[idx] = {};
                this.spellValues[idx].teleport_zone = val;
            } else {
                this.teleport_zone = val;
            }
            this.closeModal();
        },

        // Determine the type of selector modal to show based on the selected slot's effect id.
        selectedTargetType() {
            const i = Number(this.selectedIndex) || 0;
            if (!i) return null;
            const id = Number(this.spellValues[i]?.effectid) || 0;
            return this.getModalTypeForEffectId(id);
        },

        // User-friendly label for the selector button based on the selected slot's effect id
        // (e.g. "Teleport to Zone" vs "Pet"). Falls back to "Teleport Zone" if no SPA or unrecognized SPA is selected.
        selectedTargetLabel() {
            const t = this.selectedTargetType();
            if (!t) return 'Teleport Zone';
            switch (t) {
                case 'zones': return 'Teleport to Zone';
                case 'pets': return 'Pet';
                case 'horses': return 'Horse';
                case 'auras': return 'Aura';
                default: return 'Teleport Zone';
            }
        },

        watchEffectId(i) {
            this.$watch(
                () => this.spellValues[i]?.effectid,
                async (newSpa, oldSpa) => {
                    if (!newSpa || newSpa === oldSpa) return;

                    await this.loadExampleValues(i, newSpa);
                }
            );
        },

        pickTypedSelect(selector, expectedPath) {
            const nodes = Array.from(document.querySelectorAll(selector));
            if (!nodes || nodes.length === 0) return null;
            if (nodes.length === 1) return nodes[0];

            for (const el of nodes) {
                const anc = el.closest('[x-data]');
                if (!anc) continue;
                const xdata = anc.getAttribute('x-data') || '';
                if (xdata.includes('ajaxSelect') && xdata.includes(expectedPath)) return el;
            }

            for (const el of nodes) {
                if (el.offsetParent !== null) return el;
            }

            return nodes[0];
        },

        async populateTypedField(i, field) {
            const type = this.spaFieldType(i, field);
            const value = this.spellValues[i][`effect_${field}_value`] ?? null;

            if (!value) return;

            try {
                //if (type.includes('item')) {
                if (this.isItemField(i, field)) {
                    const res = await fetch(`/items/search?id=${value}`);
                    if (!res.ok) return;
                    const item = await res.json();

                    const selector = `select[name="effect_${field}_value${i}"]`;
                    const select = this.pickTypedSelect(selector, '/items/search');
                    if (!select) return;

                    // wait for TomSelect to be initialized on this select
                    if (!select.tomselect) return;

                    const label = item.name ?? item.Name ?? item.title ?? item.label ?? String(value);
                    select.tomselect.addOption({ id: value, name: `${value}: ${label}`, icon: item.icon ?? null });
                    select.tomselect.setValue(value, true);
                    return;
                }

                //if (type.includes('spell')) {
                if (this.isSpellField(i, field)) {
                    const res = await fetch(`/spells/search?id=${value}`);
                    if (!res.ok) return;
                    const spell = await res.json();

                    const selector = `select[name="effect_${field}_value${i}"]`;
                    const select = this.pickTypedSelect(selector, '/spells/search');
                    if (!select) return;

                    if (!select.tomselect) return;

                    const label = spell.name ?? spell.Name ?? spell.title ?? spell.label ?? String(value);
                    select.tomselect.addOption({ id: value, name: `${value}: ${label}`, new_icon: spell.new_icon ?? null });
                    select.tomselect.setValue(value, true);
                    return;
                }
            } catch (e) {
                console.error('populateTypedField failed', i, field, e);
            }
        },

        async populateTypedDefaults(i, field) {
            await this.populateTypedField(i, field);
        },

        async loadExampleValues(i, spaId) {
            const res = await fetch(`/spells/defaults/${spaId}`);
            const data = await res.json();
            if (!data) return;

            Object.assign(this.spellValues[i], {
                effect_base_value: data.base ?? 0,
                effect_limit_value: data.limit ?? 0,
                max: data.max ?? 0,
                formula: data.formula ?? 0,
            });

            // handle item / spell typed fields
            this.populateTypedField(i, 'base');
            this.populateTypedField(i, 'limit');
            this.populateTypedField(i, 'max');
        },
    };
});

Alpine.store('aaSpellCache', {
    _promise: null,
    spells: {},
    items: {},
    pendingSpellIds: new Set(),
    pendingItemIds: new Set(),

    flush() {
        if (this._promise) return this._promise;
        this._promise = new Promise(resolve => {
            setTimeout(() => resolve(this._doFetch()), 0);
        });
        return this._promise;
    },

    async _doFetch() {
        const spellIds = [...this.pendingSpellIds];
        const itemIds  = [...this.pendingItemIds];

        const [spellRes, itemRes] = await Promise.all([
            spellIds.length
                ? fetch(`/spells/search?ids=${spellIds.join(',')}`).then(r => r.ok ? r.json() : []).catch(() => [])
                : Promise.resolve([]),
            itemIds.length
                ? fetch(`/items/search?ids=${itemIds.join(',')}`).then(r => r.ok ? r.json() : []).catch(() => [])
                : Promise.resolve([]),
        ]);

        spellRes.forEach(s => { this.spells[s.id] = s; });
        itemRes.forEach(i => { this.items[i.id] = i; });
    },
});

Alpine.data('aaRankEffects', () => ({
    effects: {},
    effectsArray: [],
    spaDefs: {},
    _hydrated: false,

    init() {
        try {
            const ev = this.$el.dataset.effects;
            this.effects = ev ? JSON.parse(ev) : {};
            this.effectsArray = Object.values(this.effects);

            const topSpaDefsEl = document.querySelector('[data-spa-defs]');
            this.spaDefs = topSpaDefsEl ? JSON.parse(topSpaDefsEl.dataset.spaDefs) : {};

            this.collectTypedIdsToStore();

            Alpine.store('aaSpellCache').flush();
        } catch (e) {
            console.error('aaRankEffects init error', e);
        }
    },

    async hydrate() {
        if (this._hydrated) return;
        this._hydrated = true;

        await Alpine.store('aaSpellCache').flush();

        this.$el.setAttribute('data-ts-active', '');

        this.$el.querySelectorAll('[x-data]').forEach(el => {
            el.dispatchEvent(new Event('ts:init'));
        });

        this.$nextTick(() => {
            this.effectsArray.forEach((_, idx) => {
                this.watchEffectId(idx);
                this.populateTypedField(idx, 'base1');
                this.populateTypedField(idx, 'base2');
            });
        });
    },

    collectTypedIdsToStore() {
        const store = Alpine.store('aaSpellCache');

        this.effectsArray.forEach((e, idx) => {
            if ((this.isSpellField(idx, 'base1') || this.isLimitSpellField(idx, 'base1')) && e.base1) {
                store.pendingSpellIds.add(Math.abs(e.base1));
            }

            if ((this.isSpellField(idx, 'base2') || this.isLimitSpellField(idx, 'base2')) && e.base2) {
                store.pendingSpellIds.add(Math.abs(e.base2));
            }

            if (this.isItemField(idx, 'base1') && e.base1) {
                store.pendingItemIds.add(e.base1);
            }

            if (this.isItemField(idx, 'base2') && e.base2) {
                store.pendingItemIds.add(e.base2);
            }
        });
    },

    spaFieldKey(field) {
        return field === 'base1' ? 'base' : 'limit';
    },

    spaFieldType(idx, field) {
        const spaId = Number(this.effectsArray[idx]?.effectid);
        const raw = this.spaDefs?.[spaId]?.[field];
        if (!raw) return 'number';
        return raw.toString().trim().toLowerCase();
    },

    isItemField(idx, field) {
        const spaId = Number(this.effectsArray[idx]?.effectid);
        const type = this.spaDefs?.[spaId]?.[this.spaFieldKey(field)];
        if (!type) return false;

        const normalized = type.toString().toLowerCase();
        return normalized.includes('item id');
    },

    isSpellField(idx, field) {
        const spaId = Number(this.effectsArray[idx]?.effectid);
        const type = this.spaDefs?.[spaId]?.[this.spaFieldKey(field)];
        if (!type) return false;

        const normalized = type.toString().toLowerCase().replace(/[\s_]+/g, '');
        return normalized.includes('spellid');
    },

    isLimitSpellField(idx, field) {
        const spaId = Number(this.effectsArray[idx]?.effectid);
        return spaId === 139 && field === 'base1';
    },

    watchEffectId(idx) {
        this.$watch(
            () => this.effectsArray[idx]?.effectid,
            async (newSpa, oldSpa) => {
                if (!newSpa || newSpa === oldSpa) return;
                await this.loadDefaults(idx, newSpa);
            }
        );
    },

    async loadDefaults(idx, spaId) {
        try {
            const res = await fetch(`/spells/defaults/${spaId}`);
            if (!res.ok) return;

            const data = await res.json();
            if (!data) return;

            this.effectsArray[idx].base1 = data.base ?? 0;
            this.effectsArray[idx].base2 = data.limit ?? 0;

            await this.ensureTypedInCache(idx, 'base1');
            await this.ensureTypedInCache(idx, 'base2');

            this.populateTypedField(idx, 'base1');
            this.populateTypedField(idx, 'base2');
        } catch (e) {
            console.error('AA defaults load failed', e);
        }
    },

    async ensureTypedInCache(idx, field) {
        const raw = this.effectsArray[idx]?.[field];
        if (!raw) return;

        const absVal = Math.abs(Number(raw));
        if (!absVal) return;

        const store = Alpine.store('aaSpellCache');

        if ((this.isSpellField(idx, field) || this.isLimitSpellField(idx, field)) && !store.spells[absVal]) {
            try {
                const res = await fetch(`/spells/search?ids=${absVal}`);
                if (res.ok) {
                    const data = await res.json();
                    data.forEach(s => { store.spells[s.id] = s; });
                }
            } catch (e) {
            }
        }

        if (this.isItemField(idx, field) && !store.items[absVal]) {
            try {
                const res = await fetch(`/items/search?ids=${absVal}`);
                if (res.ok) {
                    const data = await res.json();
                    data.forEach(i => { store.items[i.id] = i; });
                }
            } catch (e) {
            }
        }
    },

    async populateTypedField(idx, field) {
        const valueRaw = this.effectsArray[idx]?.[field];
        if (!valueRaw) return;

        const value = Math.abs(Number(valueRaw));

        this.$nextTick(() => {
            const select = this.$el.querySelector(
                `select[data-field="${field}"][data-idx="${idx}"]`
            );

            if (!select) return;

            const tryApply = () => {
                const ts = select.tomselect;
                if (!ts) return false;

                const store = Alpine.store('aaSpellCache');

                if (this.isSpellField(idx, field)) {
                    const spell = store.spells[value];
                    if (!spell) return true;

                    const opt = { id: String(value), name: `${value}: ${spell.name}` };
                    if (spell.new_icon) opt.new_icon = spell.new_icon;

                    ts.addOption(opt);
                    ts.setValue(String(value), true);
                }

                if (this.isItemField(idx, field)) {
                    const item = store.items[value];
                    if (!item) return true;

                    const opt = { id: String(value), name: `${value}: ${item.name}` };
                    if (item.icon) opt.icon = item.icon;

                    ts.addOption(opt);
                    ts.setValue(String(value), true);
                }

                return true;
            };

            let attempts = 0;
            const interval = setInterval(() => {
                attempts++;
                if (tryApply() || attempts > 20) {
                    clearInterval(interval);
                }
            }, 50);
        });
    },

    addRow() {
        const maxSlot = this.effectsArray.reduce(
            (max, e) => Math.max(max, Number(e.slot) || 0), 0
        );
        const idx = this.effectsArray.length;
        this.effectsArray.push({
            slot: maxSlot + 1,
            effectid: 0,
            base1: 0,
            base2: 0,
            effectLabel: null,
        });

        this.$nextTick(() => this.watchEffectId(idx));
    },

    removeRow(idx) {
        this.effectsArray.splice(idx, 1);
    }
}));

window.addEventListener('ranks:saveAll', () => {
    try {
        const s = Alpine.store('rankSaver');
        if (s && typeof s.saveAll === 'function') {
            s.saveAll();
        } else {
            console.warn('rankSaver.saveAll not available');
        }
    } catch (e) {
        console.error('ranks:saveAll handler error', e);
    }
});

Alpine.data('floatingPanel', (opts = {}) => ({
    open: false,
    init() {
        try {
            this.$watch('open', (v) => { if (!v) this.resetPanel(); });
        } catch (e) {
        }
    },

    openPanel() {
        // If already open, close
        if (this.open) { this.open = false; this.resetPanel(); return; }

        try {
            const btn = this.$refs.trigger;
            const panel = this.$refs.panel;
            if (!btn || !panel) { this.open = true; return; }

            // temporarily show the panel offscreen/hidden to measure without flicker
            const prevDisplay = panel.style.display;
            const prevVisibility = panel.style.visibility;
            const prevPosition = panel.style.position;

            panel.style.position = 'fixed';
            panel.style.visibility = 'hidden';
            panel.style.display = 'block';

            // allow layout to settle
            this.$nextTick(() => {
                try {
                    const btnRect = btn.getBoundingClientRect();
                    const panelW = panel.offsetWidth || panel.getBoundingClientRect().width;
                    const panelH = panel.offsetHeight || panel.getBoundingClientRect().height;

                    // compute left so panel stays within viewport
                    let left = Math.round(btnRect.left);
                    if (left + panelW > window.innerWidth) left = Math.max(8, Math.round(window.innerWidth - panelW - 8));
                    if (left < 8) left = 8;

                    // compute top
                    let top = Math.round(btnRect.bottom);
                    if (top + panelH > window.innerHeight) {
                        top = Math.round(btnRect.top - panelH);
                        if (top < 8) top = 8;
                    }

                    panel.style.left = `${left}px`;
                    panel.style.top = `${top}px`;
                    panel.style.right = 'auto';
                    panel.style.bottom = 'auto';
                    panel.style.visibility = 'visible';
                    this.open = true;
                } catch (e) {
                    panel.style.display = prevDisplay;
                    panel.style.visibility = prevVisibility;
                    panel.style.position = prevPosition;
                    this.open = true;
                }
            });
        } catch (e) {
            this.open = true;
        }
    },

    resetPanel() {
        try {
            const panel = this.$refs.panel;
            if (!panel) return;
            panel.style.position = '';
            panel.style.left = '';
            panel.style.top = '';
            panel.style.right = '';
            panel.style.bottom = '';
        } catch (e) { }
    }
}));

Alpine.data('formPreview', (formSelector = null, resourceType = 'spells') => ({
    effectDescOpen: false,
    effectDescTitle: '',
    effectDescBody: '',
    timer: null,
    debounceMs: 300,
    init() {
        let dirty = false;
        let attached = false;

        const updatePreviewKey = (name, value) => {
            const container = this.$refs.container;
            if (!container) return;
            const els = container.querySelectorAll('[data-preview-key="' + CSS.escape(name) + '"]');
            els.forEach(el => {
                if (el.classList.contains('spell-icon')) {
                    el.className = el.className.split(' ').filter(c => !c.startsWith('spell-')).join(' ') + ' spell-' + value;
                    return;
                }
                el.textContent = value === null || value === undefined ? '' : value;
            });
        };

        const getPreviewUrls = () => {
            return {
                spells: '/spells/preview',
                items: '/items/preview',
                npcs: '/npcs/preview',
            };
        };

        const attachToForm = (form) => {
            if (!form || attached) return;
            attached = true;

            // mark dirty on any input/change inside the form
            form.addEventListener('input', () => { dirty = true; }, true);
            form.addEventListener('change', () => { dirty = true; }, true);

            const readValueFor = (field) => {
                if (field.type === 'checkbox') return field.checked ? field.value : (field.dataset.offValue ?? '0');
                if (field.type === 'radio') {
                    const sel = form.querySelector('[name="' + CSS.escape(field.getAttribute('name')) + '"]:checked');
                    return sel ? sel.value : '';
                }
                return field.value;
            };

            form.querySelectorAll('[name]').forEach(field => {
                const mode = field.getAttribute('data-preview');
                const name = field.getAttribute('name');
                if (!mode || !name) return;

                if (mode === 'live') {
                    field.addEventListener('input', () => {
                        try { console.debug('preview:live input', name, readValueFor(field)); } catch (e) { }
                        updatePreviewKey(name, readValueFor(field));
                    });
                }

                if (mode === 'blur' || mode === 'blur-post') {
                    field.addEventListener('blur', () => {
                        try { console.debug('preview:blur', name, readValueFor(field)); } catch (e) { }
                        updatePreviewKey(name, readValueFor(field));
                        if (mode === 'blur-post') {
                            if (dirty) send();
                        }
                    }, true);
                }
            });

            const send = async () => {
                try {
                    const fd = new FormData(form);
                    const obj = {};
                    fd.forEach((v, k) => {
                        if (obj[k] === undefined) obj[k] = v;
                        else if (Array.isArray(obj[k])) obj[k].push(v);
                        else obj[k] = [obj[k], v];
                    });

                    const urls = getPreviewUrls();
                    const url = (urls && urls[resourceType]) ? urls[resourceType] : (urls.spells || '/spells/preview');
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(obj)
                    });

                    if (!res.ok) return;
                    const json = await res.json();
                    if (json.html) {
                        const container = this.$refs.container;
                        const existingDbstr = container ? container.querySelector('[data-preview-key="dbstr_desc"]') : null;
                        let preservedDbstrBlock = null;
                        if (existingDbstr) {
                            const wrapper = existingDbstr.closest('div.col-span-3') || existingDbstr.parentElement;
                            const heading = wrapper && wrapper.previousElementSibling && wrapper.previousElementSibling.tagName === 'H2'
                                ? wrapper.previousElementSibling.outerHTML
                                : '';

                            preservedDbstrBlock = heading + (wrapper ? wrapper.outerHTML : existingDbstr.outerHTML);
                        }

                        container.innerHTML = json.html;

                        if (preservedDbstrBlock && !container.querySelector('[data-preview-key="dbstr_desc"]')) {
                            container.insertAdjacentHTML('beforeend', preservedDbstrBlock);
                        }

                        dirty = false;
                    }
                } catch (e) {
                    console.error('preview fetch failed', e);
                }
            };

            const handler = () => {
                if (!dirty) return;
                clearTimeout(this.timer);
                this.timer = setTimeout(send, this.debounceMs);
            };

            form.addEventListener('blur', handler, true);
        };


        const explicit = (formSelector && typeof formSelector === 'string') ? document.querySelector(formSelector) : null;
        if (explicit) {
            attachToForm(explicit);
        } else {
            //console.warn('formPreview: form selector not provided or element not found:', formSelector);
        }

        window.addEventListener('effect-desc', (e) => {
            const d = e?.detail || {};
            const spaId = d.id || null;
            const def = d.def || null;

            if (!def) {
                this.effectDescTitle = spaId ? ('Effect ' + spaId) : '';
                this.effectDescBody = '<p class="text-sm text-neutral-300">No description available.</p>';
                this.effectDescOpen = true;
                return;
            }

            const name = def.effectName || (spaId ? ('Effect ' + spaId) : '');
            const desc = def.description || '';
            const base = def.base !== undefined ? def.base : '';
            const limit = def.limit !== undefined ? def.limit : '';
            const max = def.max !== undefined ? def.max : '';
            const notes = def.notes !== undefined ? def.notes : '';

            const dl = `
                <dl class="grid grid-cols-3 gap-1 divide-y divide-base-content/10 [&>:not(:last-child)]:pb-2">
                    <dt class="font-medium">Base</dt>
                    <dd class="col-span-2">${base}</dd>
                    <dt class="font-medium">Limit</dt>
                    <dd class="col-span-2">${limit}</dd>
                    <dt class="font-medium">Max</dt>
                    <dd class="col-span-2">${max}</dd>
                </dl>`;

            const notesHtml = notes ? (`<h3 class="font-medium mt-4 text-accent">Notes</h3><p class="text-sm text-neutral-300 mt-2">${notes}</p>`) : '';

            this.effectDescTitle = name;
            this.effectDescBody = (desc ? `<p class="mb-2">${desc}</p>` : '') + dl + notesHtml;
            this.effectDescOpen = true;
        });

        window.addEventListener('effect-desc-hide', () => {
            this.effectDescOpen = false;
        });
    }
}));

// lookup dbstr
Alpine.data('dbstrLookup', (type = 6, fieldName = 'descnum') => ({
    init() {

        let input = this.$el;
        if (!input) return;

        if (input.tagName && input.tagName.toLowerCase() !== 'input') {
            input = input.querySelector(`input[name="${fieldName}"]`);
            if (!input) return;
        }

        let timer = null;
        const doLookup = async () => {
            const id = (input.value || '').toString().trim();
            if (!id) {
                const el = document.querySelector('[data-preview-key="dbstr_desc"]');
                if (el) el.innerHTML = '';
                return;
            }

            try {
                const res = await fetch(`/dbstr/lookup?type=${encodeURIComponent(type)}&id=${encodeURIComponent(id)}`);
                if (!res.ok) {
                    console.error('dbstr lookup failed', res.status);
                    return;
                }
                const json = await res.json();
                const val = json?.value ?? '';
                const el = document.querySelector('[data-preview-key="dbstr_desc"]');
                if (el) {
                    el.innerHTML = val || '';
                }
            } catch (e) {
                console.error('dbstr lookup error', e);
            }
        };

        input.addEventListener('blur', () => doLookup());
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(doLookup, 400);
        });
    }
}));

Alpine.data('progressInput', (initialValue = 0, unit = 'raw', allowDecimals = false) => {

    const secondsMode = ['s', 'sec', 'secs', 'second', 'seconds'].includes(String(unit).toLowerCase());
    const scale = secondsMode ? 1000 : 1;
    const initialMs = secondsMode ? Number(initialValue || 0) * 1000 : Number(initialValue || 0);

    return {
        valMs: initialMs,
        secondsMode,
        allowDecimals,

        get display() {
            const converted = this.secondsMode ? this.valMs / 1000 : this.valMs;

            return this.allowDecimals ? converted : Math.round(converted);
        },

        set display(v) {
            let num = Number(v) || 0;

            if (!this.allowDecimals) {
                num = Math.round(num);
            }

            this.valMs = this.secondsMode ? Math.round(num * 1000) : num;
        },

        init() {
            this.$nextTick(() => {
                this._applyForVal(this.valMs);

                this.$watch('valMs', (v) => {
                    this._applyForVal(v);
                });
            });
        },

        _applyForVal(v) {
            const el = this.$refs.fill;
            if (!el) return;

            if (!v || Number(v) === 0) {
                el.style.transition = 'none';
                el.style.width = '100%';

                if (el.__castAnimFrame) {
                    cancelAnimationFrame(el.__castAnimFrame);
                    el.__castAnimFrame = null;
                }

                return;
            }

            el.style.transition = '';

            if (Alpine.store('casttime')?.restart) {
                // should be ms
                Alpine.store('casttime').restart(el, v);
            } else {
                el.style.width = '100%';
            }
        }
    };
});

Alpine.data('argbColorPicker', (initialDec = 0) => {
    const decInit = Number(initialDec) >>> 0;
    const r0 = (decInit >>> 16) & 0xFF;
    const g0 = (decInit >>> 8) & 0xFF;
    const b0 = decInit & 0xFF;

    return {
        hex: '#' + [r0, g0, b0].map(n => n.toString(16).padStart(2, '0')).join(''),
        dec: decInit >>> 0,

        init() {
            // normalize hex and ensure dec matches our conversion (force alpha=255)
            this.hex = String(this.hex || '#000000').toLowerCase();

            this.$nextTick(() => {
                const input = this.$el.querySelector('input.coloris');
                if (input) {
                    if (input.value && input.value !== this.hex) {
                        this.hex = input.value;
                    }

                    input.addEventListener('input', (e) => {
                        if (input.value !== this.hex) {
                            this.hex = input.value;
                            this.updateDec();
                        }
                    });
                }
            });

            this.updateDec();
        },

        updateDec() {
            let val = String(this.hex || '').trim();
            let r = 0, g = 0, b = 0;

            if (val.startsWith('#')) {
                let hex = val.replace('#', '').padEnd(6, '0').slice(0, 6);
                if (hex.length === 6) {
                    r = parseInt(hex.substring(0, 2), 16) || 0;
                    g = parseInt(hex.substring(2, 4), 16) || 0;
                    b = parseInt(hex.substring(4, 6), 16) || 0;
                } else if (hex.length === 3) {
                    r = parseInt(hex[0] + hex[0], 16) || 0;
                    g = parseInt(hex[1] + hex[1], 16) || 0;
                    b = parseInt(hex[2] + hex[2], 16) || 0;
                }
            } else {
                const rgbaMatch = val.match(/rgba?\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})/i);
                if (rgbaMatch) {
                    r = Number(rgbaMatch[1]) || 0;
                    g = Number(rgbaMatch[2]) || 0;
                    b = Number(rgbaMatch[3]) || 0;
                }
            }

            r = Math.max(0, Math.min(255, Math.round(r)));
            g = Math.max(0, Math.min(255, Math.round(g)));
            b = Math.max(0, Math.min(255, Math.round(b)));

            const aByte = 0xFF;
            const dec = ((aByte << 24) >>> 0) | (r << 16) | (g << 8) | b;
            this.dec = dec >>> 0;
            // keep hex normalized
            this.hex = '#' + [r, g, b].map(n => n.toString(16).padStart(2, '0')).join('');
        }
    };
});

Alpine.data('fieldWatcher', (fieldName, compareValue = -1) => {
    return {
        fieldName: fieldName,
        cmpRaw: (typeof compareValue === 'object' && compareValue !== null) ? (compareValue.cmp ?? -1) : compareValue,
        extraRule: (typeof compareValue === 'object' && compareValue !== null) ? (compareValue.extraRule ?? null) : null,
        val: undefined,
        _el: null,
        _observer: null,

        init() {
            // find visible input/select/textarea first, fallback to any element with the name
            this._el = document.querySelector(`[name="${this.fieldName}"]:not([type="hidden"])`)
                || document.querySelector(`[name="${this.fieldName}"]`);

            // init value
            this.read();

            if (!this._el) {
                // nothing to watch
                return;
            }

            this._el.addEventListener('input', () => this.read());
            this._el.addEventListener('change', () => this.read());

            try {
                this._observer = new MutationObserver(() => this.read());
                this._observer.observe(this._el, {
                    attributes: true,
                    attributeFilter: ['value'],
                    childList: true,
                    subtree: true
                });
            } catch (e) {
            }

            this.read();
        },

        read() {
            if (!this._el) {
                this.val = undefined;
                return;
            }
            let raw = this._el.value;

            if (this._el.type === 'checkbox') {
                raw = this._el.checked ? (this._el.value ?? '1') : (this._el.hasAttribute('data-unchecked-value') ? this._el.getAttribute('data-unchecked-value') : '');
            }
            this.val = this._coerce(raw);
        },

        _coerce(v) {
            if (v === null || v === undefined || v === '') return v;
            // numeric-like?
            const n = Number(v);
            if (!Number.isNaN(n) && String(v).trim() !== '') return n;
            return String(v);
        },

        _cmpValue() {
            return this._coerce(this.cmpRaw);
        },

        isNot(cmp = undefined) {
            const target = cmp === undefined ? this._cmpValue() : this._coerce(cmp);
            if (this.val === undefined) return false; // no element found -> hide by default
            if (this.extraRule !== null && Number(this.extraRule) === 0) return false;

            return this.val !== target;
        },

        isEqual(cmp = undefined) {
            const target = cmp === undefined ? this._cmpValue() : this._coerce(cmp);
            return this.val === target;
        },

        isTruthy() {
            return !!this.val;
        },

        destroy() {
            if (this._observer) this._observer.disconnect();
        }
    };
});

Alpine.data('bitMaskPicker', (opts = {}) => {
    const rawInitial = opts.initial;
    const fieldName = opts.fieldName || 'augtype';

    return {
        fieldName,
        value: 0,
        checked: [],
        allChecked: false,

        init() {
            const boxes = () => Array.from(this.$root.querySelectorAll('input[type="checkbox"][value]:not([data-all])'));

            const applyInitial = (v) => {
                const initial = Number((v ?? (typeof rawInitial === 'function' ? rawInitial() : rawInitial)) || 0);
                const boxList = boxes();
                this.checked = boxList
                    .map(b => Number(b.value))
                    .filter(bit => (initial & bit) !== 0);

                this.value = this.checked.reduce((acc, vv) => acc | Number(vv), 0);
                this.updateAllChecked();
            };

            const storeVal = this.$store?.modalForm?.form?.[fieldName];
            applyInitial(storeVal !== undefined ? storeVal : rawInitial);

            this.$watch('checked', (arr) => {
                this.value = (arr || []).reduce((acc, v) => acc | Number(v), 0);
                this.updateAllChecked();
            }, { deep: true });

            if (this.$store && this.$store.modalForm) {
                this.$watch(() => this.$store.modalForm.form?.[fieldName], (nv) => {
                    applyInitial(nv);
                });
            }
        },

        updateAllChecked() {
            const boxes = Array.from(this.$root.querySelectorAll('input[type="checkbox"][value]:not([data-all])'));
            this.allChecked = boxes.length > 0 && this.checked.length === boxes.length;
        },

        // toggles all on/off based on current allChecked value
        toggleAll() {
            const boxes = Array.from(this.$root.querySelectorAll('input[type="checkbox"][value]:not([data-all])'));
            if (this.allChecked) {
                // check all
                this.checked = boxes.map(b => Number(b.value));
            } else {
                // uncheck all
                this.checked = [];
            }
        }
    };
});

Alpine.data('ajaxSelect', (config = {}) => ({
    ts: null,
    required: config.required ?? false,
    multiple: config.multiple ?? false,
    delimiter: config.delimiter ?? '|',
    searchUrl: config.searchUrl ?? null,
    prefillPath: config.prefillPath ?? null,
    allowNone: config.allowNone ?? false,
    noneId: config.noneId ?? -1,
    noneLabel: config.noneLabel ?? 'None',
    prefillValue: config.prefillValue ?? null,
    seedOptions: config.seedOptions ?? null,
    watchValue: config.watchValue ?? null,
    useModal: config.useModal ?? true,
    lazy: config.lazy ?? false,

    init() {
        if (this.lazy) {
            if (this.$el.closest('[data-ts-active]')) {
                this.$nextTick(() => {
                    this.ensureInitialized();
                    this.prefill();
                });
            } else {
                this.$el.addEventListener('ts:init', () => {
                    this.ensureInitialized();
                    this.prefill();
                }, { once: true });
            }
        } else {
            this.$nextTick(() => {
                this.ensureInitialized();
                this.prefill();
            });
        }

        if (this.useModal && this.$store?.modalForm) {
            this.$watch('$store.modalForm.isOpen', (open) => {
                let isInModal = false;
                try {
                    let ancestor = this.$el;
                    while (ancestor) {
                        if (ancestor.getAttribute && ancestor.hasAttribute('x-show')) {
                            const val = ancestor.getAttribute('x-show') || '';
                            if (val.includes('modalForm.isOpen')) {
                                isInModal = true;
                                break;
                            }
                        }
                        ancestor = ancestor.parentElement;
                    }
                } catch (e) {
                }

                if (!isInModal) {
                    isInModal = !!(this.$el.closest('.modal') || this.$el.closest('[role="dialog"]') || this.$el.closest('[x-cloak]'));
                }
                if (!isInModal) return;

                if (open) {
                    this.$nextTick(() => {
                        this.ensureInitialized();
                        this.prefill();
                    });
                } else {
                    this.destroy();
                }
            });
        }

        if (this.watchValue) {
            this.$watch(this.watchValue, () => {
                this.$nextTick(() => this.prefill());
            });
        }
    },
    ensureInitialized() {
        if (this.ts) return;

        if (!this.$refs.select) {
            console.warn('ajaxSelect: select ref missing');
            return;
        }

        if (typeof TomSelect === 'undefined') {
            console.error('TomSelect is not loaded');
            return;
        }

        const self = this;

        this.ts = new TomSelect(this.$refs.select, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'id'],
            preload: false,
            maxItems: this.multiple ? null : 1,
            create: false,
            plugins: this.multiple ? ['remove_button', 'clear_button', 'dropdown_input'] : '',
            maxOptions: 200,
            loadingClass: 'ts-loading',
            placeholder: 'Type to search...',
            allowEmptyOption: this.allowNone,
            onChange: (value) => {
                const empty = value === null || value === undefined || value === '' || (Array.isArray(value) && value.length === 0);
                if (empty && !self.multiple && self.allowNone) {
                    setTimeout(() => {
                        try { self.ts.setValue(self.noneId, true); } catch (e) { }
                    }, 0);
                }
            },
            render: {
                option: function (item, escape) {
                    const label = escape(item.text || item.name || item.value || item.label || '');

                    const rawIcon = (item.icon !== undefined && item.icon !== null && item.icon !== '')
                        ? item.icon
                        : (item.new_icon !== undefined && item.new_icon !== null && item.new_icon !== '' ? item.new_icon : '');
                    const iconVal = String(rawIcon).replace(/[^\w-]/g, '');

                    let iconClass = '';
                    if (item.icon !== undefined && item.icon !== null && item.icon !== '') {
                        iconClass = `item-icon item-${iconVal} item-icon-sm`;
                    } else if (item.new_icon !== undefined && item.new_icon !== null && item.new_icon !== '') {
                        iconClass = `spell-icon spell-${iconVal} spell-icon-sm`;
                    }

                    const iconHtml = iconClass
                        ? `<span class="icon-wrap flex items-center"><div class="${iconClass} w-4 h-auto self-center shrink-0" aria-hidden="true"></div></span>`
                        : ``;

                    return `
                        <div class="ts-option flex items-center gap-2">
                            ${iconHtml}
                            <div class="flex-1">${label}</div>
                        </div>
                    `;
                },

                item: function (item, escape) {
                    const label = escape(item.text || item.name || item.value || item.label || '');
                    const rawIcon = (item.icon !== undefined && item.icon !== null && item.icon !== '')
                        ? item.icon
                        : (item.new_icon !== undefined && item.new_icon !== null && item.new_icon !== '' ? item.new_icon : '');
                    const iconVal = String(rawIcon).replace(/[^\w-]/g, '');

                    let iconClass = '';
                    if (item.icon !== undefined && item.icon !== null && item.icon !== '') {
                        iconClass = `item-icon item-${iconVal} item-icon-sm`;
                    } else if (item.new_icon !== undefined && item.new_icon !== null && item.new_icon !== '') {
                        iconClass = `spell-icon spell-${iconVal} spell-icon-sm`;
                    }

                    const iconHtml = iconClass
                        ? `<span class="icon-wrap inline-flex items-center align-middle"><span class="${iconClass} w-4 h-auto" aria-hidden="true"></span></span>`
                        : ``;

                    return `
                        <span class="ts-selected inline-flex items-center gap-2 align-middle">
                            ${iconHtml}
                            <span class="inline-block align-middle leading-tight">${label}</span>
                        </span>
                    `;
                }
            },
            load: async (query, callback) => {
                if (!query.length || !this.searchUrl) return callback();

                try {
                    const res = await fetch(
                        `${this.searchUrl}?q=${encodeURIComponent(query)}`
                    );
                    const data = await res.json();

                    callback(
                        data.map(item => {
                            const id = item.id ?? item.ID;
                            const label = `${id}: ${item.name ?? item.Name ?? item.title ?? item.label ?? ''}`;
                            const spellIcon = item.new_icon;
                            const itemIcon = item.icon;

                            return {
                                id,
                                name: label,
                                ...(spellIcon ? { new_icon: spellIcon } : (itemIcon ? { icon: itemIcon } : {}))
                            };
                        })
                    );
                } catch (e) {
                    console.error('ajaxSelect load error', e);
                    callback();
                }
            },
        });

        if (this.seedOptions) {
            const seeds = Array.isArray(this.seedOptions)
                ? this.seedOptions
                : Object.entries(this.seedOptions).map(([id, name]) => ({ id, name }));

            seeds.forEach(opt => {
                try {
                    const id = String(opt.id ?? opt.value ?? opt.ID ?? opt);
                    const name = opt.name ?? opt.label ?? opt.title ?? String(id);
                    if (!this.ts.options[id]) {
                        const spellIcon = opt.new_icon;
                        const itemIcon = opt.icon;
                        const option = { id, name };

                        if (spellIcon) {
                            option.new_icon = spellIcon;
                        } else if (itemIcon) {
                            option.icon = itemIcon;
                        }

                        this.ts.addOption(option);
                    }
                } catch (e) {
                }
            });
        }

        // Inject "None" option
        if (this.allowNone) {
            this.ts.addOption({
                id: this.noneId,
                name: this.noneLabel,
            });
        }
    },
    prefill() {
        if (!this.ts) return;

        let source = null;

        if (this.prefillValue !== null && this.prefillValue !== undefined) {
            source = typeof this.prefillValue === 'function'
                ? this.prefillValue()
                : this.prefillValue;
        }

        const hasSource = !(source === null || source === undefined
            || (typeof source === 'string' && String(source).trim() === '')
            || (Array.isArray(source) && source.length === 0));

        // If no prefill source but `allowNone` is enabled, select the None option.
        if (!hasSource) {
            if (this.allowNone) {
                try { this.ts.setValue(this.noneId, true); } catch (e) { }
            }
            return;
        }

        let items = [];

        if (this.multiple) {
            if (Array.isArray(source)) {
                items = source;
            } else if (typeof source === 'string') {
                items = source
                    .split(this.delimiter)
                    .filter(Boolean)
                    .map(id => ({ id }));
            } else {
                items = [source];
            }
        } else {
            items = [source];
        }

        let ids = [];

        items.forEach(item => {
            if (item === null || item === undefined) return;

            const id =
                item.id ??
                item.ID ??
                item.value ??
                item.activityid ??
                item.zoneidnumber ??
                item;

            if (id === null || id === undefined) return;

            const label =
                item.name ??
                item.Name ??
                item.label ??
                item.title ??
                item.short_name ??
                String(id);

            const idStr = String(id);

            if (!this.ts.options[idStr]) {
                const spellIcon = item ? item.new_icon : null;
                const itemIcon = item ? item.icon : null;
                const option = { id: idStr, name: `${idStr}: ${label}` };

                if (spellIcon) {
                    option.new_icon = spellIcon;
                } else if (itemIcon) {
                    option.icon = itemIcon;
                }

                this.ts.addOption(option);
            }

            ids.push(idStr);
        });

        ids = ids.map(v => String(v));
        this.ts.setValue(ids, true);
    },
    // spell editing specific
    async populateTypedField(i, field) {
        const type = this.spaFieldType(i, field);
        const value = this.spellValues[i][`effect_${field}_value`] ?? null;

        if (!value) return;

        if (type === 'item id') {
            await this.lookupAndInject(
                'items',
                value,
                `effect_${field}_value${i}`
            );
        }

        if (type === 'spellid') {
            await this.lookupAndInject(
                'spells',
                value,
                `effect_${field}_value${i}`
            );
        }
    },
    // spell editing specific
    async populateTypedDefaults(i, field) {
        const type = this.spaFieldType(i, field);
        if (!type) return;

        let id = null;

        if (field === 'base') id = this.spellValues[i].effect_base_value;
        if (field === 'limit') id = this.spellValues[i].effect_limit_value;
        if (field === 'max') id = this.spellValues[i].max;

        if (!id || id <= 0) return;

        // item fetch
        if (type === 'item id') {
            const res = await fetch(`/items/${id}`);
            if (!res.ok) return;
            const item = await res.json();

            Alpine.store('modalForm').form.item = {
                id: item.id,
                name: item.name,
            };
        }

        // spell fetch
        if (type === 'spellid') {
            const res = await fetch(`/spells/${id}`);
            if (!res.ok) return;
            const spell = await res.json();

            Alpine.store('modalForm').form.spell = {
                id: spell.id,
                name: spell.name,
            };
        }
    },
    // spell editing specific
    async lookupAndInject(type, id, inputName) {
        try {
            const res = await fetch(`/${type}/${id}`);
            const data = await res.json();
            const selector = `select[name="${inputName}"]`;
            const expectedPath = `/${type}`;
            const select = this.pickTypedSelect(selector, expectedPath);
            if (!select) return;

            if (!select.tomselect) return;

            const label = data.name ?? data.Name ?? data.title ?? data.label ?? '';
            const spellIcon = data.new_icon;
            const itemIcon = data.icon;
            const option = { id, name: `${id}: ${label}` };

            if (spellIcon) {
                option.new_icon = spellIcon;
            } else if (itemIcon) {
                option.icon = itemIcon;
            }

            select.tomselect.addOption(option);
            select.tomselect.setValue(id, true);
        } catch (e) {
            console.error('lookupAndInject failed', type, id, e);
        }
    },
    destroy() {
        if (!this.ts) return;

        this.ts.destroy();
        this.ts = null;
    },
}));

// lazy load race select
Alpine.data('raceSelect', (i, initial = null) => ({
    loaded: false,
    options: [],
    name: '',
    display: '',
    currentId: null,
    async init() {
        if (this._initRun) return;
        this._initRun = true;

        let id = null;
        if (initial !== null && initial !== undefined) {
            id = Number(initial) || null;
        }
        if (!id) {
            try { id = Number(this.$root?.spellValues?.[i]?.effect_base_value) || null; } catch (e) { id = null; }
        }

        if (id) {
            this.currentId = id;
            try {
                const res = await fetch(`/npcs/races/${id}`);
                if (res.ok) {
                    const d = await res.json();
                    this.name = d.label ?? '';
                } else {
                    this.name = '';
                }
            } catch (e) {
                this.name = '';
            }
            this.display = this.name ? `${id}: ${this.name}` : String(id);

            this.$nextTick(() => {
                const sel = this.$el.querySelector('select');
                if (!sel) return;

                const existing = Array.from(sel.options).find(o => String(o.value) === String(id));
                if (!existing) {
                    const opt = document.createElement('option');
                    opt.value = String(id);
                    opt.text = this.display || String(id);
                    sel.prepend(opt);
                } else {
                    existing.text = this.display || existing.text;
                }
                sel.value = String(id);
            });
        }
    },
    async load() {
        if (this.loaded) return;
        try {
            const res = await fetch('/npcs/races');
            if (!res.ok) return;
            const data = await res.json();
            // Avoid duplicating the already-prepended currentId option
            const entries = Object.entries(data);
            if (this.currentId) {
                this.options = entries.filter(e => String(e[0]) !== String(this.currentId));
            } else {
                this.options = entries;
            }
            this.loaded = true;
        } catch (e) {
        }
    }
}));

// spell effect select: reads/writes a hidden input and shows a friendly label.
Alpine.data('spellEffectSelect', (slot = 1, initial = null, field = 'base') => ({
    localKey: 0,
    name: '',
    display: '',
    mode: 'auto', // auto or exclude
    signedValue: '',
    loaded: false,
    options: [],
    _initRun: false,

    async init() {
        if (this._initRun) return;
        this._initRun = true;

        // hidden input name pattern: effect_base_value1 or effect_limit_value1
        const inputName = `effect_${field}_value${slot}`;
        const hidden = this.$el.querySelector(`input[type=hidden][name="${inputName}"]`);

        let raw = hidden?.value ?? initial ?? '';
        raw = String(raw ?? '').trim();

        if (raw.startsWith('-')) {
            this.mode = 'exclude';
            this.localKey = Math.abs(Number(raw)) || 0;
        } else {
            this.mode = 'auto';
            this.localKey = Number(raw) || 0;
        }

        this.signedValue = raw;

        if (this.localKey !== null && this.localKey !== undefined) {
            await this._fetchLabel(Number(this.localKey));
        }

        this.$watch('localKey', () => this._applyToHidden());
        this.$watch('mode', () => this._applyToHidden());
    },

    async _fetchLabel(id) {
        try {
            id = Number(id) || 0;
            const res = await fetch(`/spells/spelleffects/${id}`);
            if (!res.ok) return;
            const ct = res.headers.get('content-type') || '';
            if (ct.includes('application/json')) {
                const data = await res.json();
                this.name = data.name ?? data.label ?? data.title ?? (data[1] ?? String(data));
            } else {
                const text = await res.text();
                try {
                    const json = JSON.parse(text);
                    this.name = json.name ?? json.label ?? json.title ?? text;
                } catch (e) {
                    this.name = text;
                }
            }
            this.display = `${id}: ${this.name}`;
        } catch (e) {
            console.debug('spellEffectSelect: fetch label failed', e);
        }
    },

    async load() {
        if (this.loaded) return;
        try {
            const res = await fetch('/spells/spelleffects');
            if (!res.ok) return;
            const ct = res.headers.get('content-type') || '';
            let data = [];
            if (ct.includes('application/json')) {
                data = await res.json();
            } else {
                const text = await res.text();
                try { data = JSON.parse(text); } catch (e) { data = []; }
            }

            // normalize into unique [id,label] list
            const map = new Map();
            if (Array.isArray(data)) {
                for (let idx = 0; idx < data.length; idx++) {
                    const d = data[idx];
                    if (d === null || d === undefined) continue;
                    let id;
                    let label = '';
                    if (typeof d === 'string' || typeof d === 'number' || typeof d === 'boolean') {
                        id = idx;
                        label = String(d);
                    } else if (Array.isArray(d)) {
                        id = Number(d[0]);
                        label = d[1] ?? String(d[0]);
                    } else if (typeof d === 'object') {
                        id = Number(d.id ?? d[0] ?? idx);
                        label = d.name ?? d.label ?? d.title ?? String(id);
                    }

                    if (!Number.isNaN(id)) map.set(String(id), label);
                }
            } else if (data && typeof data === 'object') {
                for (const [k, v] of Object.entries(data)) {
                    const id = Number(k);
                    const label = typeof v === 'string' ? v : (v?.name ?? v?.label ?? v?.title ?? '');
                    if (!Number.isNaN(id)) map.set(String(id), label);
                }
            }

            this.options = Array.from(map.entries()).map(([k, v]) => [String(k), v]);
            this.options.sort((a, b) => Number(a[0]) - Number(b[0]));

            const lk = Number(this.localKey);
            if (!this.name && !Number.isNaN(lk)) {
                const found = this.options.find(opt => Number(opt[0]) === lk);
                if (found) {
                    this.name = found[1];
                    this.display = `${lk}: ${this.name}`;
                }
            }

            this.loaded = true;
        } catch (e) {
            console.debug('spellEffectSelect: load list failed', e);
        }
    },

    _applyToHidden() {
        const inputName = `effect_${field}_value${slot}`;
        const hidden = this.$el.querySelector(`input[type=hidden][name="${inputName}"]`);
        let signed = '';
        const key = Number(this.localKey) || 0;
        if (key === 0) signed = '0';
        else signed = (this.mode === 'exclude' ? '-' : '') + String(key);

        this.signedValue = signed;

        if (hidden) {
            hidden.value = signed;
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
            hidden.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
}));

// Limit-Spell (SPA 139) select: uses an ajaxSelect for spell search
// but keeps a hidden signed input (positive => auto, negative => exclude).
// - aa editor which reads from aaSpellCache store
// - spell editor falls back to individual /spells/search fetch
Alpine.data('limitSpellSelect', (slot = 1, initial = null, field = 'base') => ({
    localKey: 0,
    name: '',
    display: '',
    mode: 'auto', // auto or exclude
    signedValue: '',

    async init() {

        const raw = String(initial ?? '').trim();

        if (raw.startsWith('-')) {
            this.mode = 'exclude';
            this.localKey = Math.abs(Number(raw)) || 0;
        } else {
            this.mode = 'auto';
            this.localKey = Number(raw) || 0;
        }

        this._updateSigned();

        if (this.localKey) {
            await this._resolveLabel(this.localKey);
        }

        const hasAa = this._hasAaCache();

        if (hasAa && this.$el.closest('[data-ts-active]')) {
            this.$nextTick(() => this._seedTomSelect());
        } else if (hasAa) {
            this._seedTomSelect();
        } else {
            this._seedTomSelect();
        }

        const inner = this.$el.querySelector('select[data-limit-spell]');
        if (inner) {
            inner.addEventListener('change', (ev) => {
                const v = Number(ev.target.value) || 0;
                if (v === this.localKey) return;
                this.localKey = v;
                this._updateSigned();
                if (v) this._resolveLabel(v).then(() => this._seedTomSelect());
            });
        }

        this._syncHidden();

        this.$watch('localKey', () => { this._updateSigned(); this._syncHidden(); });
        this.$watch('mode', () => { this._updateSigned(); this._syncHidden(); });
    },

    _hasAaCache() {
        try { return !!Alpine.store('aaSpellCache'); } catch (e) { return false; }
    },

    async _resolveLabel(id) {
        if (this._hasAaCache()) {
            const store = Alpine.store('aaSpellCache');
            await store.flush();
            const spell = store.spells[id];
            if (spell) {
                this.name = spell.name;
                this.display = `${id}: ${spell.name}`;
                return;
            }
        }

        try {
            const res = await fetch(`/spells/search?ids=${id}`);
            if (!res.ok) return;
            const data = await res.json();
            const spell = Array.isArray(data) ? data[0] : data;
            if (!spell) return;

            this.name = spell.name ?? String(id);
            this.display = `${id}: ${this.name}`;

            if (this._hasAaCache()) {
                Alpine.store('aaSpellCache').spells[id] = spell;
            }
        } catch (e) {
            console.debug('limitSpellSelect: fetch failed', e);
        }
    },

    _seedTomSelect() {
        const inner = this.$el.querySelector('select[data-limit-spell]');
        if (!inner || !this.localKey) return;
        const apply = () => {
            const t = inner.tomselect || inner.TomSelect || inner.tomSelect || inner._tomselect;
            if (!t) return false;

            const id    = String(this.localKey);
            const label = this.display || id;
            const opt   = { id, name: label };

            if (this._hasAaCache()) {
                const spell = Alpine.store('aaSpellCache').spells[this.localKey];
                if (spell?.new_icon) opt.new_icon = spell.new_icon;
            }

            try {
                t.addOption(opt);
                t.setValue(id, true);
                // verify tom selects are getting populated
            } catch (e) {
            }

            return true;
        };

        let attempts = 0;
        const interval = setInterval(() => {
            attempts++;
            const ok = apply();
            if (ok || attempts > 60) {
                clearInterval(interval);
            }
        }, 50);
    },

    // used for spell editor effects tab.
    _syncHidden() {
        const inputName = `effect_${field}_value${slot}`;
        const hidden = this.$el.querySelector(`input[type=hidden][name="${inputName}"]`);
        if (hidden && hidden.value !== this.signedValue) {
            hidden.value = this.signedValue;
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
        }
    },

    _updateSigned() {
        const key = Number(this.localKey) || 0;
        this.signedValue = key === 0 ? '0' : (this.mode === 'exclude' ? '-' : '') + String(key);
    }
}));

Alpine.data('inlineField', ({ field, value, updateUrl }) => ({
    value,
    field,
    original: value,
    saving: false,
    saved: false,
    error: false,

    isBoolean() {
        return this.value === 'true' || this.value === 'false'
    },

    async save(newValue) {
        if (newValue === this.value) return

        this.saving = true
        this.saved = false
        this.error = false

        const oldValue = this.value
        this.value = newValue

        try {
            const res = await fetch(updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name=csrf-token]')
                        .content,
                },
                body: JSON.stringify({
                    field: this.field,
                    value: newValue,
                }),
            })

            if (!res.ok) throw new Error()

            this.saved = true
            setTimeout(() => (this.saved = false), 1200)
        } catch (e) {
            this.value = oldValue
            this.error = true
        } finally {
            this.saving = false
        }
    },
}));

Alpine.data('recipeEntries', (recipeId) => ({
    recipeId,
    modalOpen: false,
    modalTitle: '',
    mode: 'create',
    type: null,
    form: {},

    openEntryModal(type, entry = null) {
        this.type = type;
        this.mode = entry ? 'edit' : 'create';
        this.modalTitle = entry ? 'Edit Entry' : 'Add Entry';

        this.form = entry ?? {
            recipe_id: this.recipeId,
            item_id: null,
            successcount: 0,
            failcount: 0,
            componentcount: 0,
            salvagecount: 0,
            iscontainer: type === 'container' ? 1 : 0,
        };

        this.modalOpen = true;
    },

    closeModal() {
        this.modalOpen = false;
        this.form = {};
    },

    get showCountFields() {
        return this.type === 'component';
    },

    get showResultFields() {
        return this.type === 'success' || this.type === 'fail';
    },

    saveEntry() {
        const url = this.mode === 'edit'
            ? `/tradeskill-entries/${this.form.id}`
            : `/tradeskills/${this.recipeId}/entries`;

        fetch(url, {
            method: this.mode === 'edit' ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            },
            body: JSON.stringify(this.form)
        }).then(() => window.location.reload());
    }
}));

Alpine.data('bookPreview', () => ({
    bookType: 0,
    layout: null,
    linesPerPage: 12,
    pageIndex: 0,
    text: '',

    init() {
        this.setLayout(this.bookType);

        const store = Alpine.store('modalForm');
        if (store?.form?.txtfile) {
            this.text = store.form.txtfile;
        }

        this.$watch(
            () => Alpine.store('modalForm')?.form?.txtfile,
            value => {
                this.text = value || '';

                try {
                    if (this.bookType === 1) {
                        const maxPages = Math.max(0, Math.ceil(this.book2Pages.length / 2));
                        if (this.pageIndex >= maxPages) {
                            this.pageIndex = Math.max(0, maxPages - 1);
                        }
                    } else {
                        const maxPages = Math.max(0, this.pages.length);
                        if (this.pageIndex >= maxPages) {
                            this.pageIndex = Math.max(0, maxPages - 1);
                        }
                    }
                } catch (e) {
                }
            }
        );
    },

    toggleBook(type) {
        this.bookType = type;
        this.pageIndex = 0;
        this.setLayout(type);
    },

    get pages() {
        if (!this.text) return [];

        const lines = this.normalizeText(this.text);
        const pages = [];
        const perPage = this.linesPerPage;

        for (let i = 0; i < lines.length; i += perPage) {
            const chunk = lines.slice(i, i + perPage);
            const html = chunk
                .map(l => {
                    if (l === '') return '<div>&nbsp;</div>';
                    const esc = l
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                    return `<div>${esc}</div>`;
                })
                .join('');
            pages.push(html);
        }

        return pages;
    },

    get book2Pages() {
        if (!this.text) return [];

        const wrappedLines = this.buildWrappedLines(this.text);
        return this.linesToPages(wrappedLines, 12);
    },

    normalizeText(text) {
        return text
            .replace(/`{3}/g, '\n\n\n')
            .replace(/`{2}/g, '\n\n')
            .replace(/`/g, '\n')
            .split('\n');
    },

    wrapLineSmart(line, maxChars) {
        const lines = [];
        let remaining = line;

        while (remaining.length > maxChars) {
            let breakAt = remaining.lastIndexOf(' ', maxChars);

            if (breakAt === -1) {
                breakAt = maxChars;
            }

            let left = remaining.slice(0, breakAt);
            let right = remaining.slice(breakAt);

            if (right.startsWith(' ')) {
                left += ' ';
                right = right.slice(1);
            }

            left = left.replace(/\s+$/, '');
            lines.push(left);
            remaining = right;
        }

        if (remaining.length) {
            lines.push(remaining.replace(/\s+$/, ''));
        }

        return lines;
    },

    buildWrappedLines(text) {
        const normalized = this.normalizeText(text);
        const wrapped = [];

        normalized.forEach(line => {
            if (line === '') {
                wrapped.push('');
                return;
            }

            const chunks = this.wrapLineSmart(line, 31);
            wrapped.push(...chunks);
        });

        return wrapped;
    },

    linesToPages(lines, linesPerPage) {
        const pages = [];
        for (let i = 0; i < lines.length; i += linesPerPage) {
            pages.push(lines.slice(i, i + linesPerPage));
        }
        return pages;
    },

    nextPage() {
        const maxPages =
            this.bookType === 1
                ? Math.ceil(this.book2Pages.length / 2)
                : this.pages.length;

        if (this.pageIndex + 1 < maxPages) {
            this.pageIndex++;
        }
    },

    prevPage() {
        if (this.pageIndex > 0) this.pageIndex--;
    },

    setLayout(type) {
        if (type === 0) {
            this.layout = {
                image: '/images/books/book_0.png',
                width: 404,
                height: 443,
                lineHeight: 16,
                text: { x: 60, y: 98, width: 285, height: 235 }
            };
            // @TODO hopefully this is actually infinity, I never checked actually.
            this.linesPerPage = Infinity;
        } else {
            this.layout = {
                image: '/images/books/book_1.png',
                width: 503,
                height: 333,
                lineHeight: 16,
                text: {
                    height: 210,
                    left: { x: 55, y: 40, width: 155 },
                    right: { x: 265, y: 40, width: 155 }
                }
            };
            this.linesPerPage = 12;
        }
    }
}));

Alpine.data('merchantSelector', ({ zone, version, npc }) => ({
    zone,
    version,
    npc,

    changeZone(zoneId) {
        window.location = `/merchants?zone=${zoneId}&v=0`;
    },

    changeVersion(v) {
        window.location = `/merchants?zone=${this.zone}&v=${v}`;
    },

    changeNpc(npcId) {
        window.location = `/merchants?zone=${this.zone}&v=${this.version}&npc=${npcId}`;
    }
}));

Alpine.data('zoneSelector', ({ zone, version }) => ({
    zone,
    version,
    changeZone(zoneId) {
        if (!zoneId) return;
        window.location = `/zones/${zoneId}/edit`;
    },
    changeVersion(version) {
        if (!version) return;
        window.location = `/zones/${version}/edit`;
    },
}));

Alpine.data('factionSelector', ({ faction }) => ({
    faction,
    changeFaction(factionId) {
        if (!factionId) return;
        window.location = `/factions/edit?faction=${factionId}`;
    },
}));

Alpine.data('factionModForm', () => ({
    type: '',
    modValue: null,
    raw: '',

    init() {
        this.$watch(
            () => this.$store.modalForm.isOpen,
            (isOpen) => {
                if (!isOpen) return
                this._syncFromModal()
            }
        )

        if (this.$store.modalForm.isOpen) {
            this._syncFromModal()
        }
    },

    _syncFromModal() {
        const form = this.$store.modalForm.form
        if (!form) return
        this.raw = form.mod_name ?? ''
        this.parse(this.raw)
    },

    parse(raw) {
        if (!raw) return
        const map = { r: 'race', c: 'class', d: 'deity' }
        this.type = map[raw.charAt(0)] ?? ''
        const val = parseInt(raw.slice(1), 10)
        const strVal = Number.isNaN(val) ? null : String(val)
        // Set modValue after options have rendered for the new type
        this.$nextTick(() => { this.modValue = strVal })
    },

    options() {
        if (!window.eqFactionModOptions || !this.type) return {}
        return window.eqFactionModOptions[this.type] ?? {}
    },

    prefix() {
        return { race: 'r', class: 'c', deity: 'd' }[this.type] ?? ''
    },

    encoded() {
        if (!this.type || this.modValue === null) return ''
        return `${this.prefix()}${this.modValue}`
    },
}));

Alpine.data('selectHydrator', (config) => ({
    loaded: false,

    init() {
        const select = this.$el;
        const currentValue = config.get?.();

        // If the current value matches the "none" id, show the none option
        if (config.allowEmpty && (currentValue === undefined || currentValue === null || currentValue === '' || String(currentValue) === String(config.noneId ?? ''))) {
            const empty = document.createElement('option');
            empty.value = config.noneId ?? '';
            empty.textContent = config.noneLabel ?? 'None';
            empty.selected = true;
            select.appendChild(empty);
        } else if (currentValue !== undefined && currentValue !== null && currentValue !== '') {
            const opt = document.createElement('option');
            opt.value = currentValue;
            const label = config.getLabel?.() ?? currentValue;
            opt.textContent = label;
            opt.selected = true;
            select.appendChild(opt);
        }
    },

    async load() {
        if (this.loaded) return;

        const select = this.$el;
        const currentValue = config.get?.();

        const response = await fetch(config.url);
        const items = await response.json();

        // clear all
        select.options.length = 0;

        // add "empty" option first if allowEmpty
        if (config.allowEmpty) {
            const empty = document.createElement('option');
            empty.value = config.noneId ?? '';
            empty.textContent = config.noneLabel ?? 'None';
            select.appendChild(empty);
        }

        let foundSelected = false;

        // populate options from fetch
        items.forEach(item => {
            const value = item[config.valueKey];
            const label = item[config.labelKey];

            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = label;

            if (String(value) === String(currentValue)) {
                opt.selected = true;
                foundSelected = true;
            }

            select.appendChild(opt);
        });

        // if current value wasn't in the result, add it
        if (currentValue !== undefined && currentValue !== null && currentValue !== '' && !foundSelected) {
            const opt = document.createElement('option');
            opt.value = currentValue;
            opt.textContent = config.getLabel?.() ?? currentValue;
            opt.selected = true;
            select.prepend(opt);
        }

        let selectedIndex = -1;
        for (let i = 0; i < select.options.length; i++) {
            if (String(select.options[i].value) === String(currentValue)) {
                selectedIndex = i;
                break;
            }
        }

        if (selectedIndex >= 0) {
            select.selectedIndex = selectedIndex;
            setTimeout(() => {
                try {
                    const opt = select.options[selectedIndex];
                    if (opt && typeof opt.scrollIntoView === 'function') {
                        opt.scrollIntoView({ block: 'nearest' });
                    }
                    select.selectedIndex = selectedIndex;
                } catch (e) {
                    //console.error('selectHydrator: scrollIntoView failed', e);
                }
            }, 0);
        }

        this.loaded = true;
    }
}));

Alpine.data('activitySorter', ({ reorderUrl }) => ({
    draggingEl: null,
    startIndex: null,
    persisting: false,

    init() {
        const rows = this.$el.querySelectorAll('tr');

        rows.forEach((row) => {
            const handle = row.querySelector('.drag-handle');
            if (!handle) return;

            handle.setAttribute('draggable', true);

            handle.addEventListener('dragstart', (e) => {
                if (this.persisting) {
                    e.preventDefault();
                    return;
                }

                this.draggingEl = row;
                this.startIndex = Array.from(this.$el.children).indexOf(row);
                row.classList.add('opacity-50');
            });

            handle.addEventListener('dragend', () => {
                if (!this.draggingEl) return;

                this.draggingEl.classList.remove('opacity-50');
                const endIndex = Array.from(this.$el.children).indexOf(this.draggingEl);

                if (this.startIndex !== endIndex) {
                    this.updateActivityIds();
                    this.persistOrder();
                }

                this.draggingEl = null;
                this.startIndex = null;
            });

            row.addEventListener('dragover', (e) => {
                e.preventDefault();
                if (!this.draggingEl || row === this.draggingEl || this.persisting) return;

                const rect = row.getBoundingClientRect();
                const insertAfter = e.clientY - rect.top > rect.height / 2;

                if (insertAfter) {
                    this.$el.insertBefore(this.draggingEl, row.nextSibling);
                } else {
                    this.$el.insertBefore(this.draggingEl, row);
                }
            });
        });
    },

    persistOrder() {
        if (this.persisting) return;
        this.persisting = true;

        const order = Array.from(this.$el.children)
            .map(row => Number(row.dataset.activityid));

        fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ order }),
        })
            .then(res => res.json())
            .then(data => {
                if (data.activities) {
                    Alpine.store('taskActivities').set(data.activities);
                }
            })
            .finally(() => {
                this.persisting = false;
            });
    },

    updateActivityIds() {
        Array.from(this.$el.children).forEach((row, index) => {
            const cell = row.querySelector('[data-activity-index]');
            if (cell) {
                cell.textContent = index;
            }

            if (row.dataset.activity) {
                const activity = JSON.parse(row.dataset.activity);
                activity._display_index = index;
                row.dataset.activity = JSON.stringify(activity);
            }
        });
    },
}));

Alpine.data('modelPreview', (initialValue = '') => ({
    modelValue: initialValue,
    objectId: null,

    init() {
        this.parseModel(this.modelValue);

        this.$watch('modelValue', (val) => {
            this.parseModel(val);
        });

        this.$watch(
            () => Alpine.store('objectPicker').selectedId,
            (val) => {
                if (val) {
                    this.modelValue = 'IT' + val;
                }
            }
        );
    },

    updateFromInput(value) {
        this.modelValue = value;

        if (!value) {
            Alpine.store('objectPicker').selectedId = null;
            return;
        }

        const match = String(value).match(/\d+/);
        Alpine.store('objectPicker').selectedId = match ? Number(match[0]) : null;
    },

    parseModel(value) {
        if (!value) {
            this.objectId = null;
            return;
        }

        const match = value.match(/\d+/);
        this.objectId = match ? Number(match[0]) : null;
    }
}));

Alpine.data('npcSelector', (init) => ({
    filters: {
        q: '',
        class: '',
        zone: init.zone || '',
        version: init.version !== undefined && init.version !== null ? Number(init.version) : 0,
        per_page: 25,
    },
    versions: Array.isArray(init.versions) ? init.versions : [],
    results: [],
    loading: false,
    selectedClassName: null,
    selectedRaceId: null,
    selectedNpc: (init && init.npc) ? (typeof init.npc === 'object' ? Number(init.npc.id) : Number(init.npc)) : null,
    _initRun: false,

    async init() {
        if (this._initRun) return;
        this._initRun = true;

        const params = new URLSearchParams(window.location.search);
        if (params.has('q')) this.filters.q = params.get('q');
        if (!this.filters.zone) this.filters.zone = params.get('zone') || this.filters.zone;
        if (!this.filters.version) this.filters.version = Number(params.get('v') || params.get('version') || this.filters.version);

        if (this.filters.zone) await this.fetchVersions(this.filters.zone);

        if (this.filters.q || this.filters.zone || this.selectedNpc) await this.search();
    },

    _normalizeVersions(raw) {
        if (!Array.isArray(raw)) return [];
        return raw.map(v => {
            if (v === null || v === undefined) return null;
            if (typeof v === 'number') return { version: Number(v) };
            if (typeof v === 'string' && v.match(/^\d+$/)) return { version: Number(v) };
            if (typeof v === 'object' && v.version !== undefined) return { version: Number(v.version) };
            return null;
        }).filter(Boolean).filter(o => Number(o.version) > 0);
    },

    async fetchVersions(zoneId) {
        this.versions = [];
        if (!zoneId) return [];
        this.loading = true;
        try {
            const r = await fetch(`/api/zones/${zoneId}/versions`);
            const vs = await r.json();
            const arr = Array.isArray(vs) ? vs : [];
            this.versions = this._normalizeVersions(arr);
            const hasVersion = this.versions.some(o => Number(o.version) === Number(this.filters.version));
            if (!hasVersion) this.filters.version = 0;
        } catch (e) {
            this.versions = [];
        } finally {
            this.loading = false;
        }
        return this.versions;
    },

    async zoneChanged() {
        this.filters.version = 0;
        this.results = [];
        this.selectedNpc = null;
        if (this.filters.zone) {
            await this.fetchVersions(this.filters.zone);
            await this.search();
        }
    },

    // most of this was redone
    async search(page = 1) {
        this.loading = true;
        const qs = new URLSearchParams();
        if (this.filters.q) {
            const qtrim = String(this.filters.q).trim();
            if (/^\d+$/.test(qtrim)) qs.set('id', qtrim); else qs.set('name', this.filters.q);
        }
        if (this.filters.class) qs.set('class', this.filters.class);
        if (this.filters.zone) qs.set('zone', this.filters.zone);
        if (this.filters.version) qs.set('version', this.filters.version);

        if (!this.filters.q && this.filters.zone) {
            qs.set('per_page', 0);
        } else {
            qs.set('per_page', this.filters.per_page);
        }
        qs.set('page', page);

        const res = await fetch('/api/npcs?' + qs.toString());
        if (!res.ok) { this.loading = false; return; }
        const j = await res.json();
        this.results = j.data || [];

        // check if selectedNpc exists in results and if not, fetch single and prepend it
        if (this.selectedNpc) {
            const found = this.results.find(r => Number(r.id) === Number(this.selectedNpc));
            if (!found) {
                try {
                    const single = await fetch('/api/npcs?id=' + encodeURIComponent(this.selectedNpc));
                    if (single.ok) {
                        const sj = await single.json();
                        const entry = Array.isArray(sj.data) ? sj.data[0] : (sj.data || sj);
                        if (entry && !this.results.find(r => Number(r.id) === Number(entry.id))) this.results.unshift(entry);
                        if (entry) this.selectedNpc = Number(entry.id);
                    }
                } catch (e) {
                    console.error('npcSelector: failed to fetch selected NPC', e);
                }
            } else {
                this.selectedNpc = Number(found.id);
            }
        } else {
            const params = new URLSearchParams(window.location.search);
            const npcParam = params.get('npc');
            if (npcParam) this.selectedNpc = Number(npcParam);
        }

        this.loading = false;

        // Sync native select to the selected value deterministically using x-ref
        this.$nextTick(() => {
            try {
                const sel = (this.$refs && this.$refs.npcSelect) || this.$el.querySelector('select');
                if (!sel) return;
                const wanted = this.selectedNpc === null || this.selectedNpc === undefined ? '' : String(this.selectedNpc);
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === wanted) { sel.selectedIndex = i; break; }
                }
            } catch (e) {
                console.error('npcSelector: failed to sync select element', e);
            }
        });
    }
}));

// tab persistence
Alpine.store('tabState', {
    prefix: 'app_tab_state',

    _key(group) { return `${this.prefix}:${group}`; },

    register(groupName, inputs = []) {
        if (!groupName || !inputs || !inputs.length) return;

        const params = new URLSearchParams(window.location.search);
        const urlTab = params.get('tab');
        const hashTab = window.location.hash ? window.location.hash.replace('#', '') : null;
        const saved = sessionStorage.getItem(this._key(groupName));
        const toSet = urlTab || saved || hashTab;
        if (toSet) {
            const el = inputs.find(i => i.value === toSet);
            if (el) {
                el.checked = true;
                sessionStorage.setItem(this._key(groupName), toSet);
            }
        }

        inputs.forEach(i => i.addEventListener('change', () => {
            if (i.checked) sessionStorage.setItem(this._key(groupName), i.value);
        }));
    }
});

// Reusable ID picker, can be opened from any form and will apply
// a selected id value into the target input
Alpine.store('idPicker', {
    isOpen: false,
    // known resource fetch endpoints (relative to baseUrl)
    fetchUrls: {
        spells: `${baseUrl.replace(/\/$/, '')}/free-ids?type=spells`,
        items: `${baseUrl.replace(/\/$/, '')}/free-ids?type=items`,
        npcs: `${baseUrl.replace(/\/$/, '')}/free-ids?type=npcs`,
        tasks: `${baseUrl.replace(/\/$/, '')}/free-ids?type=tasks`,
    },
    blocks: [],
    targetSelector: null,
    fetchUrl: null,

    // accepts either an object { selector, url } or { selector, type }
    async open(opts = {}) {
        const selector = opts.selector || opts.sel || 'input[name="id"]';
        this.targetSelector = selector;

        if (opts.type && this.fetchUrls[opts.type]) {
            this.fetchUrl = this.fetchUrls[opts.type];
        } else if (opts.url) {
            this.fetchUrl = opts.url;
        }

        this.isOpen = true;

        if (!this.blocks.length || opts.url || opts.type) {
            await this.refresh();
        }
    },

    close() {
        this.isOpen = false;
        // keep fetchUrl
    },

    async refresh() {
        try {
            const url = this.fetchUrl || this.fetchUrls.spells;
            const res = await fetch(url, { credentials: 'same-origin' });
            this.blocks = res.ok ? await res.json() : [];
        } catch (e) {
            console.error('idPicker.refresh error', e);
            this.blocks = [];
        }
    },

    apply(val) {
        const el = document.querySelector(this.targetSelector || 'input[name="id"]');
        if (el) {
            el.value = val;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
            try { el.focus(); } catch (e) { }
        }
        this.close();
    }
});

Alpine.store('taskActivities', {
    items: [],

    set(activities) {
        this.items = activities;
    },

    /**
     * Build display text for a TaskActivity
     */
    describe(activity) {
        if (activity.description_override !== "") {
            return activity.description_override;
        }

        switch (Number(activity.activitytype)) {
            case 1:
                let text = `Deliver ${activity.goalcount} ${activity.item_list || 'item(s)'}`;

                if (activity.target_name && activity.target_name.trim() !== "") {
                    text += ` to ${activity.target_name}`;
                }

                return text;

            case 2:
                return `Kill ${activity.goalcount} ${activity.target_name}`;

            case 3:
                return `Loot ${activity.goalcount} ${activity.item_list || 'item(s)'}`;

            case 4:
                return `Speak with ${activity.target_name}`;

            case 5:
                return `Explore ${activity.target_name || 'area'}`;

            case 6:
                return `Tradeskill: ${activity.skill_list}`;

            case 7:
                return `Fish for ${activity.goalcount} ${activity.item_list || 'item(s)'}`;

            case 8:
                return `Forage ${activity.goalcount} ${activity.item_list || 'item(s)'}`;

            case 9:
            case 10:
                return `Use ${activity.item_list || activity.target_name}`;

            case 11:
                return `Touch ${activity.target_name}`;

            case 100:
                return `Give ${activity.goalcount} platinum`;

            default:
                return `Unknown activity (${activity.activitytype})`;
        }
    },

    /**
     * Read current activities from the table DOM
     */
    fromTable() {
        return Array.from(document.querySelectorAll('tr[data-activity]'))
            .map(row => JSON.parse(row.dataset.activity))
            .sort((a, b) => a.activityid - b.activityid);
    },

    /**
     * Build select options for req_activity_id
     */
    selectOptions({ excludeId = null } = {}) {
        return this.items
            .filter(a => a.activityid !== excludeId)
            .map(a => ({
                value: a.activityid,
                label: `${a.activityid}: ${this.describe(a)}`,
            }));
    },
});

Alpine.store('animationPicker', {
    isOpen: false,
    type: null,
    targetInput: null,
    animations: [],
    cache: {},
    selectedId: null,

    open({ type, target }) {
        this.type = type
        this.targetInput = target
        this.isOpen = true

        const input = document.querySelector(`[name="${target}"]`);
        this.selectedId = input && input.value ? String(input.value) : null;

        const loadAndScroll = () => {
            this.animations = this.cache[type]
            Alpine.nextTick(() => {
                this.scrollToSelected()
            });
        }

        if (this.cache[type]) {
            loadAndScroll();
        } else {
            this.animations = []

            fetch(`/spells/animations/list?type=${type}`)
                .then(r => r.json())
                .then(data => {
                    this.cache[type] = data;
                    loadAndScroll();
                })
        }
    },

    close() {
        this.isOpen = false
    },

    select(id) {
        this.selectedId = String(id)

        const input = document.querySelector(`[name="${this.targetInput}"]`)
        if (input) {
            input.value = id
            input.dispatchEvent(new Event('input', { bubbles: true }))
        }

        this.close()
    },

    scrollToSelected() {
        if (!this.selectedId) return

        const el = document.querySelector(
            `[data-anim-id="${this.selectedId}"]`
        )

        if (el) {
            el.scrollIntoView({
                block: 'center',
                inline: 'nearest',
                behavior: 'smooth'
            })
        }
    },

    loadVideo(el, id) {
        const video = el.querySelector('video')
        if (video.dataset.loaded) return

        video.src = `/${this.type}-animations/${id}.mp4`
        video.dataset.loaded = '1'
        video.load()
        video.play().catch(() => { })
    }
});

Alpine.store('dbstrPicker', {
    isOpen: false,
    type: 6,
    targetInput: null,
    query: '',
    results: [],
    page: 1,
    lastPage: 1,
    loading: false,
    previewValue: '',
    // create mode
    creating: false,
    createValue: '',
    createId: null,
    saving: false,
    // edit mode
    editing: false,
    editId: null,
    editValue: '',

    async open(inputId, type = 6) {
        this.type = type;
        this.targetInput = inputId;
        this.results = [];
        this.page = 1;
        this.lastPage = 1;
        this.creating = false;
        this.createValue = '';
        this.createId = null;
        this.saving = false;
        this.previewValue = '';
        this.selectedId = null;
        this.isOpen = true;

        let input = document.getElementById(this.targetInput);
        if (!input) {
            input = document.querySelector(`[name="${this.targetInput}"]`);
        }

        if (input && input.value) {
            this.query = input.value;
        } else {
            this.query = '';
        }

        await this.search();

        // focus the search input in the modal so the copied value is visible
        setTimeout(() => {
            const modalSearch = document.querySelector('#dbstr-picker-modal input[type="text"]');
            if (modalSearch) modalSearch.focus();
        }, 80);

        // look up current value for preview
        if (input && input.value && parseInt(input.value) > 0) {
            try {
                const res = await fetch(`/dbstr/lookup?type=${this.type}&id=${encodeURIComponent(input.value)}`);
                const json = await res.json();
                this.previewValue = json.value ?? '';
            } catch (e) {
                console.error('dbstrPicker lookup error', e);
            }
        }

        if (this.selectedId) {
            setTimeout(() => this.scrollToSelected(), 50);
        }
    },

    close() {
        this.isOpen = false;
        this.creating = false;
        this.createValue = '';
        this.createId = null;
        this.saving = false;
        this.editing = false;
        this.editId = null;
        this.editValue = '';
    },

    async search(page = 1) {
        this.loading = true;
        this.page = page;
        try {
            const params = new URLSearchParams({
                type: this.type,
                q: this.query,
                page: this.page,
            });
            const res = await fetch(`/dbstr/search?${params}`);
            const json = await res.json();
            this.results = json.data ?? [];
            this.lastPage = json.last_page ?? 1;
        } catch (e) {
            console.error('dbstrPicker search error', e);
        }
        this.loading = false;
    },

    select(id) {
        const input = document.getElementById(this.targetInput);
        if (!input) return;
        input.value = id;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.dispatchEvent(new Event('blur', { bubbles: true }));
        this.close();
    },

    async startCreate() {
        this.creating = true;
        this.createValue = '';
        this.createId = null;
        this.saving = false;
        try {
            const res = await fetch(`/dbstr/next-id?type=${this.type}`);
            const json = await res.json();
            this.createId = json.next_id ?? 1;
        } catch (e) {
            console.error('dbstrPicker nextId error', e);
        }
    },

    cancelCreate() {
        this.creating = false;
    },

    async saveCreate() {
        if (!this.createValue.trim() || !this.createId) return;
        this.saving = true;
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch('/dbstr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    id: this.createId,
                    type: this.type,
                    value: this.createValue,
                }),
            });
            const json = await res.json();
            if (json.success) {
                this.select(this.createId);
                Alpine.store('toast').push({
                    type: 'success',
                    title: 'Created',
                    message: `DBStr #${this.createId} created.`,
                });
            } else {
                Alpine.store('toast').push({
                    type: 'error',
                    title: 'Error',
                    message: 'Failed to create DBStr.',
                });
            }
        } catch (e) {
            Alpine.store('toast').push({
                type: 'error',
                title: 'Error',
                message: e.message || 'Failed to create DBStr.',
            });
        }
        this.saving = false;
    },
    async startEdit() {
        const input = document.getElementById(this.targetInput);
        if (!input || !input.value || Number(input.value) <= 0) return;
        this.editId = Number(input.value);
        this.editValue = this.previewValue || '';
        this.editing = true;
    },
    cancelEdit() {
        this.editing = false;
        this.editId = null;
        this.editValue = '';
    },
    async saveEdit() {
        if (!this.editId) return;
        this.saving = true;
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/dbstr/${this.type}/${this.editId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ value: this.editValue }),
            });
            const json = await res.json().catch(() => ({}));
            if (res.ok && json.success) {
                // update preview and results if present
                this.previewValue = this.editValue;
                this.results = (this.results || []).map(r => r.id === this.editId ? { ...r, value: this.editValue } : r);
                Alpine.store('toast').push({ type: 'success', title: 'Saved', message: `DBStr #${this.editId} updated.` });
                this.cancelEdit();
            } else {
                Alpine.store('toast').push({ type: 'error', title: 'Error', message: json.message || 'Failed to save DBStr.' });
            }
        } catch (e) {
            Alpine.store('toast').push({ type: 'error', title: 'Error', message: e.message || 'Failed to save DBStr.' });
        }
        this.saving = false;
    },
});

Alpine.store('evolvingPicker', {
    isOpen: false,
    targetInput: null,
    groups: [],
    loading: false,
    previewValue: '',

    async open(inputId) {
        this.targetInput = inputId;
        this.isOpen = true;
        this.loading = true;
        this.groups = [];
        this.previewValue = '';

        try {
            const res = await fetch('/items/evolving-items/options');
            const json = await res.json();
            // group by item_evo_id
            const map = new Map();
            (json || []).forEach(row => {
                const key = row.item_evo_id;
                if (!map.has(key)) map.set(key, []);
                map.get(key).push(row);
            });
            this.groups = Array.from(map.entries()).map(([evoId, items]) => ({ evoId, items }));
        } catch (e) {
            console.error('evolvingPicker fetch error', e);
            this.groups = [];
        }

        // preview current input value if present
        const input = document.getElementById(this.targetInput);
        if (input && input.value && Number(input.value) > 0) {
            const cur = Number(input.value);
            const g = this.groups.find(g => Number(g.evoId) === cur);
            if (g && g.items && g.items.length) {
                const first = g.items[0];
                this.previewValue = (first.item && first.item.Name) ? first.item.Name : `Evo ${g.evoId}`;
            }
        }

        this.loading = false;
    },

    close() {
        this.isOpen = false;
    },

    select(evoId) {
        const input = document.getElementById(this.targetInput);
        if (!input) return;
        input.value = evoId;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.dispatchEvent(new Event('blur', { bubbles: true }));
        this.close();
    },

    async ensureLoaded() {
        if (this.groups && this.groups.length) return;
        this.loading = true;
        try {
            const res = await fetch('/items/evolving-items/options');
            const json = await res.json();
            const map = new Map();
            (json || []).forEach(row => {
                const key = row.item_evo_id;
                if (!map.has(key)) map.set(key, []);
                map.get(key).push(row);
            });
            this.groups = Array.from(map.entries()).map(([evoId, items]) => ({ evoId, items }));
        } catch (e) {
            console.error('evolvingPicker ensureLoaded error', e);
            this.groups = [];
        }
        this.loading = false;
    },

    async validate(evoid, evolvinglevel = null, evomax = null, currentItemId = null) {
        await this.ensureLoaded();
        const messages = [];
        const gid = Number(evoid) || 0;
        const group = this.groups.find(g => Number(g.evoId) === gid) || null;

        const exists = !!group;
        let levelsCount = 0;
        let hasLevel = false;
        if (group) {
            const levelSet = new Set(group.items.map(r => Number(r.item_evolve_level || 0)).filter(n => n > 0));
            levelsCount = levelSet.size;
            if (evolvinglevel !== null && evolvinglevel !== undefined) {
                hasLevel = levelSet.has(Number(evolvinglevel));
            }
        }

        if (!exists) messages.push('Evolution ID not found. Use the picker to select a valid ID.');
        if (exists && levelsCount === 0) messages.push('Selected Evo ID contains no items.');
        if (evomax !== null && exists && Number(evomax) !== levelsCount) messages.push(`evomax (${evomax}) does not match available levels (${levelsCount}).`);
        if (evolvinglevel !== null && exists && !hasLevel) messages.push(`Level ${evolvinglevel} does not exist for this Evo ID.`);
        // check whether current item is part of selected group
        if (currentItemId !== null && exists) {
            const found = group.items.some(r => Number(r.item_id) === Number(currentItemId));
            if (!found) {
                messages.push(`Current item (ID ${currentItemId}) is not part of Evo ID ${gid}.`);
            }
        }

        this.validation = {
            exists,
            levelsCount,
            hasLevel,
            messages,
        };

        return this.validation;
    },
});

Alpine.store('iconPicker', {
    filter: '',
    icons: [],
    filteredIcons: [],
    visibleIcons: [],
    selectedInput: null,
    initialized: false,
    batchSize: 500,
    maxVisible: 1000,
    startIndex: 0,

    open(inputId) {
        this.selectedInput = inputId;
        document.getElementById('icon-picker-modal').checked = true;

        // defer initialization until modal is visible
        // spells:  0 - 2267
        // items: 500 - 13999
        setTimeout(() => {
            if (!this.initialized) {
                const inputEl = document.getElementById(this.selectedInput);
                if (inputEl && inputEl.dataset && inputEl.dataset.iconRange === 'spells') {
                    this.icons = Array.from({ length: 2267 - 0 + 1 }, (_, i) => (0 + i).toString());
                } else {
                    this.icons = Array.from({ length: 13999 - 500 + 1 }, (_, i) => (500 + i).toString());
                }
                this.initialized = true;
            }
            this.applyFilter().then(() => {
                this.scrollToSelectedIcon();
            });
        }, 0);
    },

    close() {
        this.visibleIcons = [];
        this.filteredIcons = [];
        this.startIndex = 0;
        document.getElementById('icon-picker-modal').checked = false;
    },

    select(id) {
        const input = document.getElementById(this.selectedInput);
        if (!input) return;

        input.value = id;

        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        input.dispatchEvent(new Event('blur', { bubbles: true }));
        this.close();
    },

    async applyFilter() {
        if (!this.filter) {
            this.filteredIcons = this.icons;
            this.startIndex = 0;
            this.updateVisibleIcons();
            return;
        }

        const q = this.filter.trim();

        if (/^\d+$/.test(q)) {
            this.filteredIcons = this.icons.filter(id => id.includes(q));
            this.startIndex = 0;
            this.updateVisibleIcons();
            return;
        }

        try {
            const resp = await fetch(`/items/search?q=${encodeURIComponent(q)}`);
            if (resp.ok) {
                const items = await resp.json();
                const iconsFromItems = Array.isArray(items)
                    ? items.map(i => i.icon).filter(x => x !== null && x !== undefined).map(String)
                    : [];

                const seen = new Set();
                const merged = [];

                for (const id of iconsFromItems) {
                    if (!seen.has(id)) { seen.add(id); merged.push(id); }
                }

                for (const id of this.icons) {
                    if (!seen.has(id) && id.includes(q.toLowerCase())) {
                        seen.add(id); merged.push(id);
                    }
                }

                this.filteredIcons = merged;
            } else {
                const lq = q.toLowerCase();
                this.filteredIcons = this.icons.filter(id => id.includes(lq));
            }
        } catch (e) {
            const lq = q.toLowerCase();
            this.filteredIcons = this.icons.filter(id => id.includes(lq));
        }

        this.startIndex = 0;
        this.updateVisibleIcons();
    },

    scrollToSelectedIcon() {
        const input = document.getElementById(this.selectedInput);
        if (!input) return;
        const val = String(input.value || '').trim();
        if (!val) return;

        const idx = this.filteredIcons.indexOf(val);
        if (idx === -1) return;

        const half = Math.floor(this.maxVisible / 2) || 10;
        this.startIndex = Math.max(0, idx - half);
        this.updateVisibleIcons();

        setTimeout(() => {
            const modal = document.getElementById('icon-picker-modal');
            if (!modal) return;
            const container = modal.parentElement.querySelector('.modal-box > div.grid');
            if (!container) return;

            const btn = container.querySelector(`button[title="${val}"]`) || container.querySelector(`.item-icon.item-${val}`)?.closest('button');
            if (btn) {
                const offset = btn.offsetTop - (container.clientHeight / 2) + (btn.clientHeight / 2);
                container.scrollTop = Math.max(0, offset);
            }
        }, 0);
    },

    updateVisibleIcons() {
        const endIndex = Math.min(this.startIndex + this.maxVisible, this.filteredIcons.length);
        this.visibleIcons = this.filteredIcons.slice(this.startIndex, endIndex);
    },

    onScroll(event) {
        const el = event.target;
        const scrollThreshold = 100;

        if (el.scrollTop + el.clientHeight >= el.scrollHeight - scrollThreshold) {
            if (this.startIndex + this.maxVisible >= this.filteredIcons.length) return;
            this.startIndex += this.batchSize;
            this.updateVisibleIcons();
        } else if (el.scrollTop <= scrollThreshold) {
            if (this.startIndex === 0) return;
            this.startIndex = Math.max(0, this.startIndex - this.batchSize);
            this.updateVisibleIcons();
        }
    }
});

Alpine.store('objectPicker', {
    isOpen: false,
    targetSelector: null,
    objects: [],
    selectedId: null,

    open(selector, objects, options = {}) {
        this.targetSelector = selector;
        this.objects = objects;

        const input = document.querySelector(selector);
        if (input?.value) {
            const match = input.value.match(/\d+/);
            this.selectedId = match ? Number(match[0]) : null;
        } else {
            this.selectedId = null;
        }

        this.appendSuffix = options.append || '';
        this.isOpen = true;

        setTimeout(() => {
            if (!this.selectedId) return;

            const el = document.querySelector(
                `[data-object-id="${this.selectedId}"]`
            );

            el?.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }, 100);
    },

    close() {
        this.isOpen = false;
    },

    select(id) {
        const input = document.querySelector(this.targetSelector);
        if (input) {
            let value = 'IT' + id;
            if (this.appendSuffix) {
                value = value + this.appendSuffix;
            }
            input.value = value;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        this.selectedId = id;
        this.close();
    }
});

Alpine.store('casttime', {
    restart(el, ms) {
        if (!el) return;
        const duration = Math.max(1, Number(ms) || 0);

        if (el.__castAnimFrame) {
            cancelAnimationFrame(el.__castAnimFrame);
            el.__castAnimFrame = null;
        }

        el.style.removeProperty('animation');
        el.style.removeProperty('transform');
        el.style.setProperty('left', '0', 'important');
        el.style.removeProperty('right');
        el.style.setProperty('display', duration > 0 ? 'block' : 'none', 'important');

        if (!duration || duration <= 0) {
            el.style.width = '100%';
            return;
        }

        const start = performance.now();
        const loop = (now) => {
            const elapsed = now - start;
            const t = (elapsed % duration) / duration;
            const remaining = 1 - t;

            el.style.width = (remaining * 100) + '%';
            el.__castAnimFrame = requestAnimationFrame(loop);
        };

        el.__castAnimFrame = requestAnimationFrame(loop);
    },

    stop(el) {
        if (!el) return;
        if (el.__castAnimFrame) {
            cancelAnimationFrame(el.__castAnimFrame);
            el.__castAnimFrame = null;
        }
        el.style.setProperty('left', '0', 'important');
        el.style.removeProperty('right');
        el.style.width = '100%';
        el.style.display = 'none';
    }
});

Alpine.store('modalForm', {
    isOpen: false,
    activeModal: null,
    mode: 'create',
    title: '',
    submitLabel: 'Save',
    saving: false,
    form: {},
    meta: {},
    formAction: '',
    submitMethod: 'POST',
    errors: {},
    errorMessage: '',

    openCreate(config = {}) {
        this.reset()
        this.errors = {};
        this.errorMessage = '';

        this.activeModal = config.modal || null;
        this.saving = false;
        this.mode = 'create';
        this.title = config.title || (config.resourceName ? `Create ${config.resourceName}` : 'Create');
        this.submitLabel = config.submitLabel || 'Create';
        this.form = config.defaults ? { ...config.defaults } : {};
        this.formAction = config.action || config.baseUrl || '';
        this.submitMethod = 'POST';
        this.resourceName = config.resourceName || null;
        this.meta = config.meta || config || {};
        this.isOpen = true;

        Alpine.nextTick(() => {
            const inputs = document.querySelectorAll('[data-default]');

            inputs.forEach(input => {
                const property = input.getAttribute('x-model').split('.').pop();

                let val = input.dataset.default;
                if (val !== "" && !isNaN(val)) {
                    val = (val.includes('.')) ? parseFloat(val) : parseInt(val);
                }

                if (this.form[property] === undefined) {
                    this.form[property] = val;
                }
            });
        });
    },
    /**
     * parcelJson may be an object or a JSON string
     * baseUrl is optional and used to build the update URL if provided
     */
    openEdit(parcelJson, action = '', options = {}) {
        this.reset()
        this.activeModal = options.modal || null;

        if (typeof options !== 'object' || options === null) {
            options = {};
        }

        this.meta = options;

        let payload = parcelJson;
        if (typeof parcelJson === 'string') {
            try { payload = JSON.parse(parcelJson); } catch (e) { payload = {}; }
        }

        // qglobal specific possibly refactor?
        if (payload.expdate && Number(payload.expdate) > 0 && typeof payload.expdate !== 'undefined') {
            payload.expdate = new Date(payload.expdate * 1000)
                .toISOString()
                .slice(0, 16);
        } else {
            payload.expdate = '';
        }

        this.normalizeBooleans(payload, options.booleanFields ?? []);

        const routeKey = options.routeKey || 'id';

        this.mode = 'edit';
        this.title = options.title ||
            (options.resourceName ? options.resourceName.startsWith('Edit ')
                ? options.resourceName
                : `Edit ${options.resourceName}`
                : 'Edit'
            );
        this.submitLabel = 'Update';
        this.form = { ...(payload || {}) };
        this.submitMethod = 'PUT';
        this.resourceName = options.resourceName || null;
        this.saving = false;
        this.errors = {};
        this.errorMessage = '';

        if (action) {
            this.formAction = action;
        } else {
            this.formAction = '';
        }

        this.isOpen = true;
    },
    close() {
        this.reset();
    },
    reset() {
        this.isOpen = false;
        this.activeModal = null;
        this.mode = 'create';
        this.form = {};
        this.meta = {};
        this.formAction = '';
        this.submitMethod = 'POST';
        this.resourceName = null;
        this.saving = false;
        this.errors = {};
        this.errorMessage = '';
    },
    // utility to set single field from JS if needed
    setField(name, value) {
        this.form = Object.assign({}, this.form, { [name]: value });
    },
    normalizeBooleans(payload, fields = []) {
        fields.forEach(field => {
            if (field in payload) {
                payload[field] = Boolean(Number(payload[field]));
            }
        });
    },
    async submit(e) {
        try {
            this.saving = true;
            this.errors = {};
            this.errorMessage = '';

            const formEl = e.target;
            const url = this.formAction || formEl.getAttribute('action') || window.location.href;
            const formData = new FormData(formEl);
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const method = (formEl.getAttribute('method') || 'POST').toUpperCase();

            const res = await fetch(url, {
                method: method,
                headers: Object.assign({
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }, token ? { 'X-CSRF-TOKEN': token } : {}),
                body: formData,
                credentials: 'same-origin'
            });

            if (res.status === 422) {
                // validation errors
                const json = await res.json().catch(() => ({}));
                this.errors = json.errors || {};
                this.errorMessage = json.message || 'Please fix the validation errors below.';
                this.saving = false;
                return;
            }

            if (!res.ok) {
                const json = await res.json().catch(() => ({}));
                this.errorMessage = json.message || `Request failed with status ${res.status}`;
                this.saving = false;
                return;
            }

            const data = await res.json().catch(() => null);

            this.saving = false;
            this.errors = {};
            this.errorMessage = '';
            this.isOpen = false;

            if (data && data.redirect) {
                window.location.assign(data.redirect);
                return;
            }

            if (this.meta && (this.meta.refreshOnSuccess || this.meta.reloadOnSuccess)) {
                window.location.reload();
                return;
            }

            if (this.meta && this.meta.onSuccess && typeof window[this.meta.onSuccess] === 'function') {
                try {
                    window[this.meta.onSuccess](data);
                } catch (e) {
                    console.error('modalForm onSuccess callback error', e);
                }
            }

        } catch (err) {
            this.errorMessage = err?.message || 'An unexpected error occurred.';
            this.saving = false;
        }
    }
});

Alpine.store('raceModelPicker', {
    isOpen: false,
    grouped: [],
    loading: false,

    load() {
        if (this.loading) return;
        this.loading = true;

        const models = [];

        for (const sheet of Array.from(document.styleSheets)) {
            try {
                const rules = sheet.cssRules || sheet.rules || [];
                for (const rule of Array.from(rules)) {
                    if (!rule.selectorText) continue;
                    const selectors = rule.selectorText.split(',');
                    for (let sel of selectors) {
                        sel = sel.trim();
                        if (!sel.startsWith('.race-model-')) continue;
                        const cls = sel.replace(/^\./, '').split(':')[0];
                        const parts = cls.replace('race-model-', '').split('-');
                        if (parts.length >= 4) {
                            const race = parseInt(parts[0], 10);
                            const gender = parseInt(parts[1], 10);
                            const texture = parseInt(parts[2], 10);
                            const helm = parseInt(parts[3], 10);
                            models.push({ className: cls, race, gender, texture, helm });
                        }
                    }
                }
            } catch (e) {
                continue;
            }
        }

        const raceMap = window.raceNames || {};
        const grouped = {};
        models.forEach(m => {
            if (!grouped[m.race]) {
                grouped[m.race] = { raceId: m.race, label: (raceMap[m.race] ? `${raceMap[m.race]} (${m.race})` : `Race ${m.race}`), items: [] };
            }
            grouped[m.race].items.push(m);
        });

        this.grouped = Object.values(grouped).sort((a, b) => a.raceId - b.raceId);
        this.loading = false;
    },

    select(m) {
        try {
            this.selectedClassName = m.className;
            this.selectedRaceId = m.race;
        } catch (e) {
        }
        const setVal = (name, val) => {
            const el = document.querySelector(`[name="${name}"]`);
            if (!el) return;
            el.value = val;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        };

        setVal('race', m.race);
        setVal('gender', m.gender);
        setVal('texture', m.texture);
        setVal('helmtexture', m.helm);

        this.close();
    },

    _highlightFromInputs() {
        try {
            const raceEl = document.querySelector('[name="race"]');
            const genderEl = document.querySelector('[name="gender"]');
            const textureEl = document.querySelector('[name="texture"]');
            const helmEl = document.querySelector('[name="helmtexture"]');

            const raceId = raceEl ? Number(raceEl.value) : null;
            const gender = genderEl ? Number(genderEl.value) : null;
            const texture = textureEl ? Number(textureEl.value) : null;
            const helm = helmEl ? Number(helmEl.value) : null;

            if (!raceId) {
                this.selectedRaceId = null;
                this.selectedClassName = null;
                return;
            }

            this.selectedRaceId = raceId;

            const group = (this.grouped || []).find(g => Number(g.raceId) === Number(raceId));
            if (!group) {
                this.selectedClassName = null;
                return;
            }

            const match = (group.items || []).find(it => Number(it.gender) === Number(gender) && Number(it.texture) === Number(texture) && Number(it.helm) === Number(helm));
            this.selectedClassName = match ? match.className : null;
        } catch (e) {
            console.warn('raceModelPicker._highlightFromInputs error', e);
        }
    },

    open() {
        this.isOpen = true;
        if (!this.grouped || this.grouped.length === 0) {
            this.load();
        }
        setTimeout(() => {
            this._highlightFromInputs();
            try {
                if (this.selectedRaceId) {
                    const el = document.querySelector(`[data-race-id="${this.selectedRaceId}"]`);
                    if (el && typeof el.scrollIntoView === 'function') {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                    }
                }
            } catch (e) {
            }
        }, 60);
    },

    close() {
        this.isOpen = false;
    },
});

Alpine.store('rankSaver', {
    saving: {},

    isSaving(id) {
        return !!this.saving[String(id)];
    },

    async saveRank(elOrId) {
        let root = null;
        let rankId = null;

        if (typeof elOrId === 'string' || typeof elOrId === 'number') {
            rankId = String(elOrId);
            root = document.querySelector(`.aa-rank-card[data-rank-id='${rankId}']`);
        } else if (elOrId instanceof Element) {
            root = elOrId;
            rankId = root.dataset.rankId;
        } else if (elOrId && elOrId.$el) {
            root = elOrId.$el;
            rankId = root?.dataset?.rankId;
        }

        if (!root || !rankId) {
            console.warn('rankSaver.saveRank: missing root or rankId', elOrId);
            return;
        }

        if (this.isSaving(rankId)) return;
        this.saving[rankId] = true;

        try {
            const main = {};
            Array.from(root.querySelectorAll('input[name]:not([name$="[]"]), select[name]:not([name$="[]"])'))
                .filter(el => !el.closest('form'))
                .forEach(el => {
                    if (el.name === '_method' || el.name === '_token') return;
                    main[el.name] = el.value;
                });

            const effects = [];
            const slotInputs = Array.from(root.querySelectorAll('input[name="slot[]"]'));
            slotInputs.forEach(si => {
                const row = si.closest('tr') || si.closest('div') || si.parentNode;
                const slotVal = si.value ?? '';
                const effSel = row ? row.querySelector('select[name="effect_id[]"]') : null;
                const base1Inp = row ? row.querySelector('input[name="base1[]"], select[name="base1[]"]') : null;
                const base2Inp = row ? row.querySelector('input[name="base2[]"], select[name="base2[]"]') : null;
                const effect_id = effSel ? effSel.value : '';
                const base1 = base1Inp ? base1Inp.value : '';
                const base2 = base2Inp ? base2Inp.value : '';
                effects.push({
                    slot: slotVal,
                    effect_id: effect_id ?? '',
                    base1: base1 ?? '',
                    base2: base2 ?? ''
                });
            });

            const prereqs = [];
            const aaIds = Array.from(root.querySelectorAll('input[name="aa_id[]"], select[name="aa_id[]"]')).map(i => i.value);
            const points = Array.from(root.querySelectorAll('input[name="points[]"]')).map(i => i.value);
            for (let i = 0; i < aaIds.length; i++) {
                prereqs.push({
                    aa_id: aaIds[i],
                    points: points[i] ?? ''
                });
            }

            const payload = { main, effects, prereqs };
            payload.main = Object.assign({ rank_id: String(rankId) }, payload.main || {});
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const res = await fetch(`/aa/ranks/${rankId}/batch-save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            });

            const respText = await res.text().catch(() => null);
            let respJson = null;
            try { respJson = respText ? JSON.parse(respText) : null; } catch (_) {}

            if (res.ok && respJson?.success) {
                Alpine.store('toast').push({ id: Date.now(), type: 'success', title: 'Saved!', message: `Rank #${rankId} saved.` });
            } else {
                const msg = respJson?.message || `Server returned ${res.status}`;
                Alpine.store('toast').push({ id: Date.now(), type: 'error', title: 'Save Failed', message: `Rank #${rankId}: ${msg}` });
            }
        } catch (e) {
            console.error('rankSaver: batch save failed', e);
            Alpine.store('toast').push({ id: Date.now(), type: 'error', title: 'Save Failed', message: `Rank #${rankId}: ${e.message}` });
        }

        this.saving[rankId] = false;
    },

    async saveAll() {
        const cards = Array.from(document.querySelectorAll('.aa-rank-card'));
        const ranks = [];

        for (const c of cards) {
            const id = c.dataset.rankId;
            if (!id) continue;

            const root = c;

            const main = {};
            Array.from(root.querySelectorAll('input[name]:not([name$="[]"]), select[name]:not([name$="[]"])'))
                .filter(el => !el.closest('form'))
                .forEach(el => {
                    if (el.name === '_method' || el.name === '_token') return;
                    main[el.name] = el.value;
                });

            const effects = [];
            // Build effects by iterating each slot input and reading values from its row
            const slotInputs = Array.from(root.querySelectorAll('input[name="slot[]"]'));
            slotInputs.forEach(si => {
                const row = si.closest('tr') || si.closest('div') || si.parentNode;
                const slotVal = si.value ?? '';
                const effSel = row ? row.querySelector('select[name="effect_id[]"]') : null;
                const base1Inp = row ? row.querySelector('input[name="base1[]"], select[name="base1[]"]') : null;
                const base2Inp = row ? row.querySelector('input[name="base2[]"], select[name="base2[]"]') : null;
                const effect_id = effSel ? effSel.value : '';
                const base1 = base1Inp ? base1Inp.value : '';
                const base2 = base2Inp ? base2Inp.value : '';
                effects.push({
                    slot: slotVal,
                    effect_id: effect_id ?? '',
                    base1: base1 ?? '',
                    base2: base2 ?? ''
                });
            });

            const prereqs = [];
            const aaIds = Array.from(root.querySelectorAll('input[name="aa_id[]"], select[name="aa_id[]"]')).map(i => i.value);
            const points = Array.from(root.querySelectorAll('input[name="points[]"]')).map(i => i.value);
            for (let i = 0; i < aaIds.length; i++) {
                prereqs.push({
                    aa_id: aaIds[i],
                    points: points[i] ?? ''
                });
            }

            ranks.push({ rankId: id, main, effects, prereqs });
        }

        if (!ranks.length) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const res = await fetch(`/aa/ranks/batch-save-multiple`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ ranks }),
            });

            const respText = await res.text().catch(() => null);
            let respJson = null;
            try { respJson = respText ? JSON.parse(respText) : null; } catch (_) {}

            if (res.ok && respJson?.success) {
                Alpine.store('toast').push({ id: Date.now(), type: 'success', title: 'Saved!', message: `All ${ranks.length} rank(s) saved.` });
            } else {
                const msg = respJson?.message || `Server returned ${res.status}`;
                Alpine.store('toast').push({ id: Date.now(), type: 'error', title: 'Batch Save Failed', message: msg });
            }
        } catch (e) {
            console.error('rankSaver: bulk save failed', e);
            Alpine.store('toast').push({ id: Date.now(), type: 'error', title: 'Batch Save Failed', message: e.message });
        }
    }
});

Alpine.store('modalCache', {
    zones: null,
    pets: null,
    horses: null,
    auras: null,
    fetching: {},
    async fetch(key) {
        if (this[key]) return this[key];
        if (this.fetching[key]) return;
        this.fetching[key] = true;
        try {
            const res = await fetch(`/spells/tz?type=${encodeURIComponent(key)}`);
            if (!res.ok) throw new Error(res.statusText);
            const json = await res.json();
            // { data: [...] }
            this[key] = Array.isArray(json.data) ? json.data : [];
            return this[key];
        } catch (e) {
            console.error('modalCache fetch error', key, e);
            this[key] = [];
            return [];
        } finally {
            this.fetching[key] = false;
        }
    }
});

// timer groups for tasks
Alpine.store('timerGroups', {
    visible: false,
    field: 'replay',
    inputName: null,
    groups: [],

    async open(detail = { field: 'replay', inputName: 'replay_timer_group' }) {
        this.field = detail.field || 'replay';
        this.inputName = detail.inputName || 'replay_timer_group';
        this.visible = true;
        try {
            const res = await fetch(`/tasks/timer-groups-detail?field=${encodeURIComponent(this.field)}`);
            if (!res.ok) { this.groups = []; return; }
            const data = await res.json();
            this.groups = (Array.isArray(data) ? data : [])
                .map(g => ({
                    id: g.id, tasks: (g.tasks || [])
                        .map(t => ({ id: t.id, title: t.title || ('Task #' + (t.id || '?')) }))
                }));
        } catch (e) {
            console.error('timer-groups fetch failed', e);
            this.groups = [];
        }
    },

    close() { this.visible = false; },

    select(g) {
        const input = document.querySelector(`input[name="${this.inputName}"]`);
        if (input) {
            input.value = g.id;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        this.close();
    }
});

Alpine.store('spellsFilter', {
    query: '',

    filter(el) {
        try {
            const q = (this.query || '').trim().toLowerCase();
            const container = (el && el.closest) ? el.closest('[data-spells-root]') : document.querySelector('[data-spells-root]');
            if (!container) return;

            container.querySelectorAll('[data-spell-slot]').forEach(slot => {
                const name = (slot.dataset.spellName || '').toLowerCase();
                const match = q === '' || (name && name.includes(q));
                slot.classList.toggle('hidden', !match);
            });

            container.querySelectorAll('[data-page]').forEach(page => {
                const anyVisible = Array.from(page.querySelectorAll('[data-spell-slot]')).some(s => !s.classList.contains('hidden'));
                page.classList.toggle('hidden', !anyVisible);
            });
        } catch (e) {
            console.debug('spellsFilter.filter error', e);
        }
    }
});

// zones filter: filter by name or zoneidnumber, and expand matching expansions
Alpine.store('zonesFilter', {
    query: '',

    filter(el) {
        try {
            const q = (this.query || '').trim().toLowerCase();
            const container = (el && el.closest) ? el.closest('[data-zones-root]') : document.querySelector('[data-zones-root]');
            if (!container) return;

            // toggle each zone item
            container.querySelectorAll('[data-zone-item]').forEach(item => {
                const name = (item.dataset.zoneName || '').toLowerCase();
                const short = (item.dataset.zoneShort || '').toLowerCase();
                const id = String(item.dataset.zoneId || '');

                let match = false;
                if (q === '') {
                    match = true;
                } else {
                    match = (name && name.includes(q)) || (short && short.includes(q)) || (id && id.includes(q));
                }

                item.classList.toggle('hidden', !match);
            });

            // for each expansion (daisyUI collapse), show/hide based on its children, and auto-open if any match
            container.querySelectorAll('[data-expansion]').forEach(exp => {
                const list = exp.querySelector('ul');
                const toggle = exp.querySelector('input[type="checkbox"]');
                if (!list) return;

                const anyVisible = Array.from(list.querySelectorAll('[data-zone-item]')).some(i => !i.classList.contains('hidden'));

                // hide whole expansion container if nothing inside matches (when searching)
                exp.classList.toggle('hidden', !anyVisible && q !== '');

                // if any visible inside, ensure the collapse is opened by checking the checkbox
                if (toggle) {
                    // open only the first expansion by default when there's no query;
                    // when searching, open expansions that contain matches
                    const isFirst = exp.dataset.expansionFirst === '1';
                    toggle.checked = (q === '') ? !!isFirst : !!anyVisible;
                }
            });
        } catch (e) {
        }
    }
});

Alpine.store('tooltip', {
    content: '',
    visible: false,
    cache: new Map(),
    tooltipEl: null,

    async loadTooltip(url, triggerEl, event) {
        if (!triggerEl) return;
        if (event && event.preventDefault) event.preventDefault();

        this.loadingUrl = url;
        this.tooltipEl = document.getElementById('global-tooltip');

        const effectsOnly = triggerEl.dataset.effectsOnly === '1';
        if (effectsOnly) {
            url += '?effects-only=1';
        }

        if (this.cache.has(url)) {
            this.content = this.cache.get(url);
            this.loadingUrl = null;
        } else {
            try {
                const response = await fetch(url);
                const data = await response.json();
                this.cache.set(url, data.html);
                this.content = data.html;
            } catch (err) {
                this.content = '<div class="text-error">Failed to load tooltip.</div>';
            }
            this.loadingUrl = null;
        }

        this.visible = true;

        requestAnimationFrame(() => {
            this.positionTooltip(event, triggerEl);
        });
    },

    hideTooltip() {
        this.visible = false;
    },

    positionTooltip(e, triggerEl) {
        const tooltip = this.tooltipEl;
        if (!tooltip || !triggerEl) return;

        tooltip.style.visibility = 'hidden';
        tooltip.style.display = 'block';

        const tooltipHeight = tooltip.offsetHeight;
        const tooltipWidth = tooltip.offsetWidth;
        const rect = triggerEl.getBoundingClientRect();
        const scrollX = window.scrollX;
        const scrollY = window.scrollY;

        let top = rect.top + rect.height / 2 - tooltipHeight / 2 + scrollY;
        let left;

        const spaceRight = window.innerWidth - (rect.right + 10);
        const spaceLeft = rect.left - 10;

        if (spaceRight >= tooltipWidth) {
            left = rect.right + 10 + scrollX;
        } else if (spaceLeft >= tooltipWidth) {
            left = rect.left - tooltipWidth - 10 + scrollX;
        } else {
            left = scrollX + rect.left + (rect.width / 2) - (tooltipWidth / 2);
        }

        const maxBottom = scrollY + window.innerHeight - 10;
        if (top + tooltipHeight > maxBottom) {
            top = maxBottom - tooltipHeight;
        }
        if (top < scrollY + 10) {
            top = scrollY + 10;
        }

        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;
        tooltip.style.visibility = 'visible';
    }
});

// @TODO after delete doesn't always remove character from list
// only used on dynamic zones edit modal for now
Alpine.store('ajaxRemover', {
    async remove(options) {

        if (options.confirmMessage && !confirm(options.confirmMessage)) return;

        try {
            const response = await fetch(options.url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Request failed');
            }

            if (options.arrayRef && Array.isArray(options.arrayRef)) {
                const key = options.arrayKey || 'id';

                const id =
                    options.id ??
                    options.removeId ??
                    null;

                if (id !== null) {
                    const index = options.arrayRef.findIndex(i => i[key] == id);
                    if (index !== -1) options.arrayRef.splice(index, 1);
                }
            }

            if (options.removeEl instanceof HTMLElement) {
                options.removeEl.remove();
            }

            // optional callback
            if (typeof options.onSuccess === 'function') {
                options.onSuccess();
            }

        } catch (error) {
            console.error(error);
            alert('Failed to remove item.');
        }
    }
});

Alpine.store('toast', {
    items: [],
    maxVisible: 10,

    init(initialToasts = []) {
        initialToasts.forEach(t => this.push(t));
    },

    push(toast) {
        toast.visible = true;
        toast.timeout = toast.timeout ?? 5000;
        toast.remaining = toast.timeout;
        toast.paused = false;
        toast.startTime = Date.now();

        if (this.items.length >= this.maxVisible) {
            this.remove(this.items[0].id);
        }

        this.items.push(toast);

        if (toast.timeout > 0) {
            toast.timer = setTimeout(() => {
                this.remove(toast.id);
            }, toast.remaining);
        }
    },

    pause(id) {
        const toast = this.items.find(t => t.id === id);
        if (!toast || toast.paused) return;

        toast.paused = true;
        clearTimeout(toast.timer);

        const elapsed = Date.now() - toast.startTime;
        toast.remaining -= elapsed;
    },

    resume(id) {
        const toast = this.items.find(t => t.id === id);
        if (!toast || !toast.paused) return;

        toast.paused = false;
        toast.startTime = Date.now();

        toast.timer = setTimeout(() => {
            this.remove(id);
        }, toast.remaining);
    },

    remove(id) {
        const toast = this.items.find(t => t.id === id);
        if (!toast) return;

        clearTimeout(toast.timer);
        toast.visible = false;

        setTimeout(() => {
            this.items = this.items.filter(t => t.id !== id);
        }, 300);
    }
});

Alpine.store('sidebar', {
    open: null,
    collapsed: localStorage.getItem('sidebar_collapsed') === 'true',

    init(initialOpen = null) {
        if (initialOpen) {
            this.open = initialOpen;
        }
    },

    toggle(section) {
        this.open = this.open === section ? null : section;
    },

    isOpen(section) {
        return this.open === section;
    },

    toggleCollapse() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('sidebar_collapsed', String(this.collapsed));
    }
});

// Store for building and parsing `special_abilities` strings for NPCs.
// Definitions below describe known special attacks and the ordered
// parameters each ability uses. The server-side canonical list lives in
// `config/everquest.php` under `special_attacks` — keep these ids in
// sync with that file.
Alpine.store('specialAbilities', {
    definitions: {
        summon: {
            id: 1,
            label: 'Summon',
            fields: ['field1', 'field2', 'field3'],
            defaults: ['1', '10000', '91']
        },
        enrage: {
            id: 2,
            label: 'Enrage',
            fields: ['flag', 'field1', 'field2', 'field3'],
            defaults: ['1', '0', '10000', '360000']
        },
        rampage: {
            id: 3,
            label: 'Rampage',
            fields: ['flag', 'field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7'],
            defaults: ['1', '20', '1', '0', '0', '0', '100', '0']
        },
        aerampage: {
            id: 4,
            label: 'AE Rampage',
            fields: ['flag', 'field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7'],
            defaults: ['1', '0', '100', '0', '0', '0', '100', '0']
        },
        flurry: {
            id: 5,
            label: 'Flurry',
            fields: ['flag', 'field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7'],
            defaults: ['1', '10', '100', '0', '0', '0', '100', '0']
        },
        rangedatk: {
            id: 11,
            label: 'Ranged Attack',
            fields: ['flag', 'field1', 'field2', 'field3', 'field4', 'field5'],
            defaults: ['1', '0', '250', '0', '0', '25']
        },
        tunnelv: {
            id: 29,
            label: 'Tunnel Vision',
            fields: ['flag', 'field1'],
            defaults: ['1', '75']
        },
        leashed: {
            id: 32,
            label: 'Leashed',
            fields: ['flag', 'field1'],
            defaults: ['1', '0']
        },
        tethered: {
            id: 33,
            label: 'Tethered',
            fields: ['flag', 'field1'],
            defaults: ['1', '0']
        },
        fleepct: {
            id: 37,
            label: 'Flee Percent',
            fields: ['flag', 'field1', 'field2'],
            defaults: ['1', '0', '0']
        },
        chasedist: {
            id: 40,
            label: 'Chase Distance',
            fields: ['flag', 'field1', 'field2', 'field3'],
            defaults: ['1', '0', '0', '0']
        },
        allowtank: {
            id: 41,
            label: 'Allow Tank',
            fields: ['flag', 'field1'],
            defaults: ['1', '1']
        },
        castingresdiff: {
            id: 43,
            label: 'Casting Resist Diff',
            fields: ['flag', 'field1'],
            defaults: ['1', '0']
        },
        counteravoid: {
            id: 44,
            label: 'Counter Avoid Damage',
            fields: ['flag', 'field1', 'field2', 'field3', 'field4', 'field5'],
            defaults: ['1', '0', '0', '0', '0', '0']
        },
        modifyavoid: {
            id: 51,
            label: 'Modify Avoid Damage',
            fields: ['flag', 'field1', 'field2', 'field3', 'field4', 'field5'],
            defaults: ['1', '0', '0', '0', '0', '0']
        },

        // checkboxes with no params
        triple_attack: { id: 6, fields: ['flag'], label: 'Triple Attack' },
        quad_attack: { id: 7, fields: ['flag'], label: 'Quad Attack' },
        dual_wield: { id: 8, fields: ['flag'], label: 'Dual Wield' },
        bane_attack: { id: 9, fields: ['flag'], label: 'Bane Attack' },
        magic_attack: { id: 10, fields: ['flag'], label: 'Magic Attack' },
        // effect immunity
        unslowable: { id: 12, fields: ['flag'], label: 'Unslowable' },
        unmezable: { id: 13, fields: ['flag'], label: 'Unmezable' },
        uncharmable: { id: 14, fields: ['flag'], label: 'Uncharmable' },
        unstunable: { id: 15, fields: ['flag'], label: 'Unstunable' },
        unsnareable: { id: 16, fields: ['flag'], label: 'Unsnareable' },
        unfearable: { id: 17, fields: ['flag'], label: 'Unfearable' },
        unpacifiable: { id: 31, fields: ['flag'], label: 'Unpacifiable' },
        // immunes
        immune_dispell: { id: 18, fields: ['flag'], label: 'Immune to Dispell' },
        immune_melee: { id: 19, fields: ['flag'], label: 'Immune to Melee' },
        immune_magic: { id: 20, fields: ['flag'], label: 'Immune to Magic' },
        immune_fleeing: { id: 21, fields: ['flag'], label: 'Immune to Fleeing' },
        immune_nonbane_melee: { id: 22, fields: ['flag'], label: 'Immune to non-Bane Melee' },
        immune_nonmagical_melee: { id: 23, fields: ['flag'], label: 'Immune to non-Magical Melee' },
        immune_aggro: { id: 25, fields: ['flag'], label: 'Immune to Aggro' },
        immune_taunt: { id: 28, fields: ['flag'], label: 'Immune to Taunt' },
        immune_ranged_attacks: { id: 46, fields: ['flag'], label: 'Immune to Ranged Attacks' },
        immune_client_damage: { id: 47, fields: ['flag'], label: 'Immune to Client Damage' },
        immune_npc_damage: { id: 48, fields: ['flag'], label: 'Immune to NPC Damage' },
        immune_client_aggro: { id: 49, fields: ['flag'], label: 'Immune to Client Aggro' },
        immune_npc_aggro: { id: 50, fields: ['flag'], label: 'Immune to NPC Aggro' },
        immune_fades: { id: 52, fields: ['flag'], label: 'Immune to Memory Fades' },
        immune_open: { id: 53, fields: ['flag'], label: 'Immune to Open' },
        immune_assassinate: { id: 54, fields: ['flag'], label: 'Immune to Assassinate' },
        immune_headshot: { id: 55, fields: ['flag'], label: 'Immune to Headshot' },
        immune_bot_aggro: { id: 56, fields: ['flag'], label: 'Immune to Bot Aggro' },
        immune_bot_damage: { id: 57, fields: ['flag'], label: 'Immune to Bot Damage' },
        // misc
        will_not_aggro: { id: 24, fields: ['flag'], label: 'Will Not Aggro' },
        resist_ranged_spells: { id: 26, fields: ['flag'], label: 'Resist Ranged Spells' },
        see_through_feign_death: { id: 27, fields: ['flag'], label: 'See through Feign Death' },
        no_buff_to_friends: { id: 30, fields: ['flag'], label: 'Does NOT buff/heal friends' },
        destructible_object: { id: 34, fields: ['flag'], label: 'Destructible Object' },
        no_harm_from_players: { id: 35, fields: ['flag'], label: 'No Harm from Players' },
        always_flee: { id: 36, fields: ['flag'], label: 'Always Flee' },
        allow_beneficial: { id: 38, fields: ['flag'], label: 'Allow Beneficial' },
        disable_melee: { id: 39, fields: ['flag'], label: 'Disable Melee' },
        ignore_root_aggro: { id: 42, fields: ['flag'], label: 'Ignore Root Aggro' },
        proximity_aggro: { id: 45, fields: ['flag'], label: 'Proximity Aggro' },
    },

    items: {},
    prevItems: {},

    // return a default for a given ability param index
    defaultFor(key, index) {
        const def = this.definitions[key];
        if (!def) return '';
        // prefer explicit per-ability defaults when available
        if (def.defaults && Array.isArray(def.defaults) && typeof def.defaults[index] !== 'undefined') {
            return String(def.defaults[index]);
        }

        const fname = def.fields[index] ?? '';
        // flag fields default to '1' (enabled)
        if (fname && fname.toLowerCase().includes('flag')) return '1';
        // target fields prefer helper first option
        if (fname && fname.toLowerCase().includes('target')) {
            if (this._helpers && Array.isArray(this._helpers[key]) && this._helpers[key].length) return String(this._helpers[key][0].value ?? '1');
            return '1';
        }

        if (fname && (fname.toLowerCase().includes('hp') || fname.toLowerCase().includes('duration') || fname.toLowerCase().includes('cooldown') || fname.toLowerCase().includes('ms') || fname.toLowerCase().includes('count') || fname.toLowerCase().includes('dist') || fname.toLowerCase().includes('percent'))) return '0';

        return '0';
    },

    // initialize from existing special_abilities string (from server) and
    // optional helper data (like select options) passed via dataset on the wrapper element
    init(el) {
        if (!el) return;
        let initial = el.dataset.initial ?? '';
        // if data-initial wasn't set, try reading a hidden input inside the wrapper
        if (!initial) {
            const hidden = el.querySelector('input[name="special_abilities"]');
            if (hidden && hidden.value) initial = hidden.value;
        }
        this.parse(initial);
        // store any helper options (JSON) on the node for later UI rendering
        try {
            this._helpers = el.dataset.helpers ? JSON.parse(el.dataset.helpers) : {};
        } catch (e) {
            this._helpers = {};
        }
        // ensure DOM sync runs after other Alpine components have mounted
        try { setTimeout(() => { this.syncDOM?.(); }, 0); } catch (e) { }
    },

    // parse incoming string like "1,1,10000,91^2,1,25,10000,2340"
    parse(s) {
        this.items = {};
        if (!s) return;
        const groups = String(s).split('^').map(g => g.trim()).filter(Boolean);
        for (const g of groups) {
            const parts = g.split(',').map(p => p.trim());
            const id = Number(parts[0]) || 0;
            if (!id) continue;
            this.items[id] = parts.slice(1);
        }
        // reflect parsed values into the DOM inputs
        try { this.syncDOM?.(); } catch (e) { }
    },

    // synchronize current store values into DOM inputs/selects and checkboxes
    syncDOM() {
        try {
            for (const key of Object.keys(this.definitions)) {
                const def = this.definitions[key];
                if (!def) continue;
                const id = Number(def.id);
                const raw = Array.isArray(this.items[id]) ? this.items[id].slice() : (Array.isArray(this.prevItems[id]) ? this.prevItems[id].slice() : []);
                // pad to declared fields
                for (let i = 0; i < def.fields.length; i++) raw[i] = (typeof raw[i] === 'undefined' || raw[i] === null) ? '' : String(raw[i]);

                // compute display values: apply defaults up to lastNonEmpty (same rule as buildString)
                let lastNonEmpty = -1;
                for (let i = raw.length - 1; i >= 0; i--) {
                    if (raw[i] !== null && raw[i] !== undefined && String(raw[i]) !== '') { lastNonEmpty = i; break; }
                }
                const display = Array(def.fields.length).fill('');
                if (lastNonEmpty === -1) {
                    // show first default if enabled in items or prevItems
                    if (this.items[id] || this.prevItems[id]) {
                        display[0] = this.defaultFor(key, 0);
                    }
                } else {
                    for (let i = 0; i <= lastNonEmpty && i < def.fields.length; i++) {
                        if (raw[i] !== null && raw[i] !== undefined && String(raw[i]) !== '') {
                            display[i] = String(raw[i]);
                        } else {
                            display[i] = this.defaultFor(key, i);
                        }
                    }
                }

                const container = document.querySelector(`[data-ability="${key}"]`);
                if (!container) continue;

                // checkbox state
                const cb = container.querySelector('input[type="checkbox"]');
                if (cb) cb.checked = !!this.items[id];

                // set each param input/select value using computed display values
                for (let i = 0; i < def.fields.length; i++) {
                    const selector = `[data-param-index="${i}"]`;
                    const inpt = container.querySelector(selector);
                    if (!inpt) continue;
                    try {
                        inpt.value = display[i] ?? '';
                    } catch (e) {
                    }
                }
            }
        } catch (e) {
        }
    },

    // build string sorted by id ascending. Each item becomes id,params... and joined with ^
    buildString() {
        const ids = Object.keys(this.items).map(Number).filter(Boolean).sort((a, b) => a - b);
        const parts = ids.map(id => {
            const defKey = Object.keys(this.definitions).find(k => Number(this.definitions[k].id) === Number(id));
            const def = defKey ? this.definitions[defKey] : null;
            const vals = Array.isArray(this.items[id]) ? this.items[id].slice() : [];
            const maxFields = def ? def.fields.length : vals.length;
            // ensure we consider up to the declared fields
            while (vals.length < maxFields) vals.push('');

            // find last non-empty param index
            let lastNonEmpty = -1;
            for (let i = vals.length - 1; i >= 0; i--) {
                if (vals[i] !== null && vals[i] !== undefined && String(vals[i]) !== '') { lastNonEmpty = i; break; }
            }

            // if nothing provided, include first param default (enabled flag)
            if (lastNonEmpty === -1) {
                const firstDefault = def ? this.defaultFor(defKey, 0) : (vals[0] || '1');
                return [String(id), String(firstDefault)].join(',');
            }

            // for all params up to lastNonEmpty, fill empty slots with defaults
            const outVals = [];
            for (let i = 0; i <= lastNonEmpty; i++) {
                let v = vals[i];
                if (v === null || v === undefined || String(v) === '') {
                    v = def ? this.defaultFor(defKey, i) : '';
                }
                outVals.push(String(v));
            }

            return [String(id)].concat(outVals).join(',');
        });
        return parts.join('^');
    },

    // toggle on/off using the ability key (from definitions). If enabling, seed
    // with sensible defaults (empty strings or first helper option). If disabling,
    // remove the entry.
    toggleByKey(key, toggleEl = null) {
        const def = this.definitions[key];
        if (!def) return;
        const id = Number(def.id);
        // If currently enabled -> disable and cache current values (or DOM values)
        if (this.items[id]) {
            // try to capture current DOM values for this ability so we can restore them later
            let captured = null;
            try {
                const container = (toggleEl && toggleEl.closest) ? toggleEl.closest('[data-ability]') : document.querySelector(`[data-ability="${key}"]`);
                if (container) {
                    const vals = Array(def.fields.length).fill('');
                    for (let i = 0; i < def.fields.length; i++) {
                        const inpt = container.querySelector(`[data-param-index="${i}"]`);
                        if (inpt) vals[i] = inpt.value ?? '';
                    }
                    // keep raw values (including empty strings) if any field exists
                    if (vals.some(v => v !== null && v !== undefined && String(v) !== '')) {
                        captured = vals.map(v => (v === null || v === undefined) ? '' : String(v));
                    }
                }
            } catch (e) {
                captured = null;
            }

            this.prevItems[id] = captured ?? (Array.isArray(this.items[id]) ? this.items[id] : this.items[id]);
            delete this.items[id];
            try { this.syncDOM?.(); } catch (e) { }
            return;
        }

        // Enabling: restore from cache if available
        if (this.prevItems[id]) {
            this.items[id] = Array.isArray(this.prevItems[id]) ? this.prevItems[id].slice() : [String(this.prevItems[id])];
            delete this.prevItems[id];
            try { this.syncDOM?.(); } catch (e) { }
            return;
        }

        // Otherwise try to read existing DOM input values (user may have typed while it was hidden)
        let readVals = Array(def.fields.length).fill('');
        try {
            const container = (toggleEl && toggleEl.closest) ? toggleEl.closest('[data-ability]') : document.querySelector(`[data-ability="${key}"]`);
            if (container) {
                for (let i = 0; i < def.fields.length; i++) {
                    const inpt = container.querySelector(`[data-param-index="${i}"]`);
                    if (inpt) readVals[i] = inpt.value ?? '';
                }
            }
        } catch (e) {
            readVals = Array(def.fields.length).fill('');
        }

        // If DOM provided any non-empty param, use the raw values (defaults applied at build time)
        if (readVals.some(v => v !== null && v !== undefined && String(v) !== '')) {
            this.items[id] = readVals.map(v => (v === null || v === undefined) ? '' : String(v));
            try { this.syncDOM?.(); } catch (e) { }
            return;
        }

        // seed minimal defaults: set first param to '1' (or helper first option)
        let first = '1';
        if (this._helpers && this._helpers[key] && Array.isArray(this._helpers[key]) && this._helpers[key].length) {
            first = this._helpers[key][0]?.value ?? first;
        }
        this.items[id] = [String(first)];
        try { this.syncDOM?.(); } catch (e) { }
    },

    // update a single parameter for a given ability key
    updateParam(key, index, value) {
        const def = this.definitions[key];
        if (!def) return;
        const id = Number(def.id);
        if (!this.items[id]) {
            // start with minimal first param if index > 0 we need to pad
            this.items[id] = [];
        }
        // ensure array length
        for (let i = this.items[id].length; i <= index; i++) this.items[id].push('');
        // store raw value (allow empty string). Defaults are applied when building the string
        this.items[id][index] = (value === null || value === undefined) ? '' : String(value);
        try { this.syncDOM?.(); } catch (e) { }
    },

    // check if ability is enabled
    enabled(key) {
        const def = this.definitions[key];
        if (!def) return false;
        return !!this.items[Number(def.id)];
    },

    // expose string for UI binding
    output() {
        return this.buildString();
    }
});

Alpine.store('aaRankSlots', {
    computeNext(rootEl, selector = 'input[name="slot[]"]', newRows = []) {
        try {
            const domSlots = Array.from((rootEl || document).querySelectorAll(selector)).map(i => parseInt(i.value) || 0);
            const newSlots = Array.isArray(newRows) ? newRows.map(n => parseInt(n.slot) || 0) : [];
            const all = domSlots.concat(newSlots);
            const max = all.length ? Math.max(0, ...all) : 0;
            return max + 1;
        } catch (e) {
            return 1;
        }
    },
    reserveNext(rootEl, newRows) {
        const next = this.computeNext(rootEl, 'input[name="slot[]"]', newRows);
        try {
            if (Array.isArray(newRows)) {
                newRows.push({ slot: next, effect_id: '', base1: '', base2: '' });
            }
        } catch (e) {
        }
        return next;
    }
});

Alpine.store('idConflict', {
    open: false,
    show() { this.open = true; },
    hide() { this.open = false; },
    confirm() {
        try {
            const form = document.getElementById('spell-edit-form') || document.querySelector('form');
            if (!form) { this.hide(); return; }
            let inpt = form.querySelector('input[name="confirm_id_replace"]');
            if (!inpt) {
                inpt = document.createElement('input');
                inpt.type = 'hidden'; inpt.name = 'confirm_id_replace'; inpt.value = '1';
                form.appendChild(inpt);
            } else {
                inpt.value = '1';
            }
            form.submit();
        } catch (e) {
            console.error('idConflict.confirm failed', e);
            this.hide();
        }
    }
});

// tradeskill container templates
Alpine.store('containerTemplateForm', {
    init() {
        this.ensureItemsArray();
    },
    ensureItemsArray() {
        const modalForm = Alpine.store('modalForm');
        if (!modalForm.form.items || !Array.isArray(modalForm.form.items)) {
            modalForm.form.items = [];
        }
    },
    addItem() {
        const modalForm = Alpine.store('modalForm');
        if (!modalForm.form.items || !Array.isArray(modalForm.form.items)) {
            modalForm.form.items = [];
        }

        modalForm.form.items.push({
            item_id: null,
            item_name: ''
        });
    },
    removeItem(index) {
        const modalForm = Alpine.store('modalForm');
        if (!modalForm.form.items) return;
        modalForm.form.items.splice(index, 1);
    },
    reset() {
        Alpine.store('modalForm').form.items = [];
    }
});

Alpine.store('spa', {
    defs: {}
});

Alpine.store('update', {
    latest: null,
    hasUpdate: false,
    lastChecked: null,
    isPolling: false,
    _intervalId: null,

    _escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/[&<>"'`]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '`': '&#96;' })[c]);
    },

    async check() {
        try {
            const res = await fetch('/update/check');
            if (!res.ok) return;
            const data = await res.json();
            if (!data || !data.ok) return;

            if (data.update && data.latest) {
                this.latest = data.latest;
                const tag = data.latest.tag_name || data.latest.name || 'new';
                if (localStorage.getItem('eqemueditor_update_seen_' + tag)) {
                    this.hasUpdate = false;
                } else {
                    this.hasUpdate = true;
                }
            } else {
                this.hasUpdate = false;
                this.latest = data.latest || null;
            }

            this.lastChecked = Date.now();
        } catch (e) {
        }
    },

    start() {
        if (this.isPolling) return;
        this.isPolling = true;
        this.check();
        this._intervalId = setInterval(() => this.check(), this.POLL_INTERVAL_MS);
    },

    stop() {
        this.isPolling = false;
        if (this._intervalId) clearInterval(this._intervalId);
        this._intervalId = null;
    },

    dismiss() {
        const tag = this.latest?.tag_name || this.latest?.name;
        if (tag) localStorage.setItem('eqemueditor_update_seen_' + tag, '1');
        this.hasUpdate = false;
    },
});

// Safe helper for templates: return whether a rank is saving without throwing
window.$rankSaverIsSaving = function (id) {
    try {
        const s = Alpine.store('rankSaver');
        if (!s || typeof s.isSaving !== 'function') return false;
        return !!s.isSaving(String(id));
    } catch (e) {
        return false;
    }
};
window.evolvingValidate = async function(evoid, evolvinglevel = null, evomax = null, currentItemId = null) {
    try {
        if (!window.Alpine || typeof window.Alpine.store !== 'function') return null;
        const s = window.Alpine.store('evolvingPicker');
        if (!s || typeof s.validate !== 'function') return null;
        return await s.validate(evoid, evolvinglevel, evomax, currentItemId);
    } catch (e) {
        return null;
    }
};

window.Alpine = Alpine
window.TomSelect = TomSelect;
Alpine.start()

try {
    Coloris.init();
    Coloris({
        el: 'input.coloris',
        theme: 'pill',
        themeMode: 'dark',
        alpha: false,
    });
} catch (e) {
    console.warn('Coloris init failed', e);
}

document.addEventListener('click', () => {
    Alpine.store('tooltip')?.hideTooltip()
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        Alpine.store('tooltip')?.hideTooltip()
    }
});

document.addEventListener('scroll', () => {
    Alpine.store('tooltip')?.hideTooltip()
}, true);

document.addEventListener('DOMContentLoaded', function () {
    // preserve tab state when saving
    document.querySelectorAll('[data-tab-group]').forEach(container => {
        const group = container.getAttribute('data-tab-group');
        if (!group) return;

        const inputs = Array.from(container.querySelectorAll('input[name][value]'));
        if (!inputs.length) return;
        Alpine.store('tabState').register(group, inputs);
    });
});

(function initTomSelects() {
    const initOne = (selector, opts = {}) => {
        const el = document.querySelector(selector);
        if (!el) return;
        if (el.tomselect) return;
        if (typeof TomSelect === 'undefined') {
            setTimeout(() => initOne(selector, opts), 150);
            return;
        }

        try {
            const ts = new TomSelect(el, Object.assign({
                create: false,
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                allowEmptyOption: true,
                maxOptions: 5000,
                plugins: ['clear_button']
            }, opts));

            if (opts && opts.autoSubmitOnChange) {
                ts.on('change', () => { if (el.form) el.form.submit(); });
            }
        } catch (e) {
            console.error('TomSelect init failed for', selector, e);
        }
    };

    document.addEventListener('DOMContentLoaded', () => initOne('#ability-filter'));

    try { initOne('#ability-filter'); } catch (e) {}
})();
