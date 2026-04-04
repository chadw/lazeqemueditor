<div x-data="{ open: false, index: null, value: null }"
    @open-formula-picker.window="open = true; index = $event.detail.index; value = $event.detail.value"
    x-show="open"
    x-cloak
    x-transition
    class="bg-neutral/50 fixed inset-0 z-50 flex items-center justify-center p-6"
>
    <div class="bg-base-100 w-full max-w-2xl rounded shadow-lg overflow-hidden">
        <div class="flex items-center justify-between p-3 border-b border-base-content/10">
            <div class="font-semibold">Formula Picker</div>
            <button type="button" class="btn btn-sm btn-soft" @click="open = false">Close</button>
        </div>

        <div class="p-4">
            <label class="label">Choose formula</label>
            <select class="select w-full"
                x-model.number="value"
                @change="window.dispatchEvent(new CustomEvent('formula-picked',{
                    detail:{index:index,value:value}
                }));
                open=false"
            >
                <option value="0">0 - Base</option>
                <option value="1">1 - 1-99: Base + (Level * ID)</option>
                <option value="60">60 - Base / 100</option>
                <option value="70">70 - Base / 100</option>
                <option value="100">100 - Base</option>
                <option value="101">101 - Base + (Level / 2)</option>
                <option value="102">102 - Base + Level</option>
                <option value="103">103 - Base + (Level * 2)</option>
                <option value="104">104 - Base + (Level * 3)</option>
                <option value="105">105 - Base + (Level * 4)</option>
                <option value="107">107 - Base - BuffCalc</option>
                <option value="108">108 - Base - 2 * BuffCalc</option>
                <option value="109">109 - Base + (Level / 4)</option>
                <option value="110">110 - Base + (Level / 6)</option>
                <option value="111">111 - Base + 6 * (Level - 16)</option>
                <option value="112">112 - Base + 8 * (Level - 24)</option>
                <option value="113">113 - Base + 10 * (Level - 34)</option>
                <option value="114">114 - Base + 15 * (Level - 44)</option>
                <option value="115">115 - Base + 7 * (Level - 15)</option>
                <option value="116">116 - Base + 10 * (Level - 24)</option>
                <option value="117">117 - Base + 13 * (Level - 34)</option>
                <option value="118">118 - Base + 20 * (Level - 44)</option>
                <option value="119">119 - Base + (Level / 8)</option>
                <option value="120">120 - Base - 5 * BuffCalc</option>
                <option value="121">121 - Base + (Level / 3)</option>
                <option value="122">122 - Base - 12 * BuffCalc</option>
                <option value="123">123 - Random Value Between Base and Max</option>
                <option value="124">124 - Base + (Level - 50)</option>
                <option value="125">125 - Base + 2 * (Level - 50)</option>
                <option value="126">126 - Base + 3 * (Level - 50)</option>
                <option value="127">127 - Base + 4 * (Level - 50)</option>
                <option value="128">128 - Base + 5 * (Level - 50)</option>
                <option value="129">129 - Base + 10 * (Level - 50)</option>
                <option value="130">130 - Base + 15 * (Level - 50)</option>
                <option value="131">131 - Base + 20 * (Level - 50)</option>
                <option value="132">132 - Base + 25 * (Level - 50)</option>
                <option value="137">137 - Base - (Base * HP Ratio)</option>
                <option value="138">138 - Base * HP/(Max HP / 2)</option>
                <option value="139">139 - Base + (Level - 30) / 2</option>
                <option value="140">140 - Base + (Level - 30)</option>
                <option value="141">141 - Base + ((3 * Level) - 90) / 2</option>
                <option value="142">142 - Base + ((2 * Level) - 60)</option>
                <option value="143">143 - Base + (3 * Level / 4)</option>
                <option value="144">144 - Base + ((Level * 10) + (Level - 40) * 20)</option>
                <option value="201">201 - Max</option>
                <option value="203">203 - Max</option>
                <option value="1001">1001 - 1001-1999: Base - (ID - 1000) * BuffCalc</option>
                <option value="2000">2000 - 2000-2650: Base * (Level * (ID - 2000) + 1)</option>
            </select>
        </div>
    </div>
</div>
