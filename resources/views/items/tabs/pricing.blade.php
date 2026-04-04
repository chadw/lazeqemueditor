<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Pricing</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <x-form.input
                        name="price"
                        label="Price"
                        tooltip="The price the item should cost from a vendor (In copper currency)"
                        type="number"
                        :value="$item->price"
                    />
                    <x-form.input
                        name="sellrate"
                        label="Sell Rate"
                        tooltip="The adjusted rate that merchants will buy an item for. This is a percentage of the price where 1 is 100%. Do not set this above 1 unless you want an item to be able to be sold for more than it costs to buy."
                        type="number"
                        step="any"
                        min="0"
                        :value="$item->sellrate"
                    />
                    <x-form.input
                        name="favor"
                        label="Favor"
                        tooltip="Amount of Person Favor this item should give when turned in to favor NPC"
                        type="number"
                        :value="$item->favor"
                    />
                    <x-form.input
                        name="favor"
                        label="Guild Favor"
                        tooltip="Amount of Person Favor this item should give when turned in to favor NPC"
                        type="number"
                        :value="$item->favor"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <h2 class="card-title">LDON/DoN</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <x-form.select
                        name="pointtype"
                        label="Point Type"
                        tooltip=""
                        :options="[
                            0 => 'None',
                            1 => 'LDoN',
                            2 => 'Discord Merchant',
                            4 => 'Norrath Keeper',
                            5 => 'Dark Reign',
                        ]"
                        :selected="$item->pointtype"
                    />
                    <x-form.input
                        name="ldonprice"
                        label="LDoN Price"
                        tooltip="The price of an item in LDoN points when purchased from an LDoN merchant."
                        type="number"
                        :value="$item->ldonprice"
                    />
                    <x-form.select
                        name="ldontheme"
                        label="Theme"
                        tooltip="The LDoN Theme that this item is sold for. This correlates to the LDoN Price."
                        :options="[
                            0  => 'None',
                            1  => 'GUK',
                            2  => 'MIR',
                            4  => 'MMC',
                            8  => 'RUJ',
                            16 => 'TAK',
                            31 => 'ALL',
                        ]"
                        :selected="$item->ldontheme"
                    />
                    <x-form.input
                        name="ldonsellbackrate"
                        label="LDoN Sell Back"
                        tooltip="This is the percentage at which an item can be sold back to an LDoN merchant. Do not set above 100 (100%)."
                        type="number"
                        :value="$item->ldonsellbackrate"
                    />
                    <x-form.checkbox
                        name="ldonsold"
                        label="LDoN Sold"
                        tooltip="This defines if an item can be sold to an LDoN merchant or not."
                        :checked="$item->ldonsold"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
