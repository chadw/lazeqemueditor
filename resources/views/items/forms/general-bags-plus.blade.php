<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <h2 class="card-title">Bags</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4">
            <x-form.select
                name="bagsize"
                label="Bag Size"
                tooltip="The max size an item can be and still fit into the container. 1 = Small, 2 = Medium, 3 = Large, 4 = Giant, 5 = Giant (Assembly Kit only?)"
                :options="[
                    0 => 'Non-Bag',
                    1 => 'Small',
                    2 => 'Medium',
                    3 => 'Large',
                    4 => 'Giant',
                    5 => 'Giant - Assembly Kit',
                ]"
                :selected="$item->bagsize"
            />
            <x-form.range
                name="bagslots"
                label="Bag Slots"
                type="number"
                min="0"
                max="200"
                step="5"
                tooltip="The number of slots the bag has."
                :value="$item->bagslots"
            />
            <x-form.select
                name="bagtype"
                label="Bag Type"
                tooltip=""
                :options="[0 => 'None'] + config('everquest.bagtypes')"
                keyInOption="true"
                :selected="$item->bagtype"
            />
            <x-form.range
                name="bagwr"
                label="Bag Weight Reduction"
                type="range"
                min="0"
                max="100"
                step="5"
                tooltip=""
                :value="$item->bagwr"
            />
        </div>
    </div>
</div>
<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <h2 class="card-title">Books</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-form.select
                name="booktype"
                label="Book Type"
                tooltip="Determines the visual appearance of the book in game, example scrolls, book, parchment etc."
                :options="[
                    0  => 'None',
                    1  => 'Tattered Note',
                    2  => 'Fell Blade Guild Note',
                    3  => 'Bloodsoaked Note',
                    4  => 'Water-Stained Ancient Tome',
                    5  => 'Conium\'s Notebook Part One',
                    6  => 'The Stone Frum Pazt Vol.I',
                    7  => 'False Orders',
                    8  => 'Stupendous Tome',
                    9  => 'Leathers of the Vale',
                    10 => 'Old Delivered Book',
                    11 => 'Liber Brassica Felix v 6',
                    12 => 'Bloodstained Journal Vol 1',
                    13 => 'Guktan Warrior Recruit Letter',
                    14 => 'Foreman Naug\'s Unsigned Report',
                    15 => 'Elegant Pates',
                    16 => 'Leatherbound Journal',
                    17 => 'Dusty Kobold Scroll',
                    18 => 'The Charasis Tome',
                    19 => 'Liber Brassica Felix v 3',
                    20 => 'Liber Brassica Felix v 2',
                    21 => 'Tattered Note',
                    22 => 'Singed Scroll',
                    23 => 'Scroll of Flayed Goblin Skin',
                    25 => 'Ancient Text',
                    -1 => 'Old Parchment',
                ]"
                keyInOption="true"
                :selected="$item->booktype"
            />
            <x-form.input
                name="filename"
                label="Book (File) Name"
                tooltip="Books filename which is linked to the books table under column name"
                :value="$item->filename"
            />
        </div>
    </div>
</div>
