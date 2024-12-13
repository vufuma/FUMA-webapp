<div class="container editupdate" style="padding-top: 50px;">
    <div class="table-title">
        <div class="row">
            <div class="col-sm-10">
                @if(@isset($update))
                    <h2>Edit update: <b>{{ $update->title }} | {{ $update->version }}</b></h2>
                @else
                    <h2>Create New Update</h2>
                @endif
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <div class="options">
            <!-- Text Format -->
            <button id="bold" class="option-button format">
                <i class="fa-solid fa-bold"></i>
            </button>
            <button id="italic" class="option-button format">
                <i class="fa-solid fa-italic"></i>
            </button>
            <button id="underline" class="option-button format">
                <i class="fa-solid fa-underline"></i>
            </button>
            <button id="strikethrough" class="option-button format">
                <i class="fa-solid fa-strikethrough"></i>
            </button>
            <button id="superscript" class="option-button script">
                <i class="fa-solid fa-superscript"></i>
            </button>
            <button id="subscript" class="option-button script">
                <i class="fa-solid fa-subscript"></i>
            </button>
            <!-- List -->
            <button id="insertOrderedList" class="option-button">
                <div class="fa-solid fa-list-ol"></div>
            </button>
            <button id="insertUnorderedList" class="option-button">
                <i class="fa-solid fa-list"></i>
            </button>
            <!-- Undo/Redo -->
            <button id="undo" class="option-button">
                <i class="fa-solid fa-rotate-left"></i>
            </button>
            <button id="redo" class="option-button">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
            <!-- Link -->
            <button id="createLink" class="adv-option-button">
                <i class="fa fa-link"></i>
            </button>
            <button id="unlink" class="option-button">
                <i class="fa fa-unlink"></i>
            </button>
            <!-- Alignment -->
            <button id="justifyLeft" class="option-button align">
                <i class="fa-solid fa-align-left"></i>
            </button>
            <button id="justifyCenter" class="option-button align">
                <i class="fa-solid fa-align-center"></i>
            </button>
            <button id="justifyRight" class="option-button align">
                <i class="fa-solid fa-align-right"></i>
            </button>
            <button id="justifyFull" class="option-button align">
                <i class="fa-solid fa-align-justify"></i>
            </button>
            <button id="indent" class="option-button spacing">
                <i class="fa-solid fa-indent"></i>
            </button>
            <button id="outdent" class="option-button spacing">
                <i class="fa-solid fa-outdent"></i>
            </button>
            <!-- Headings -->
            <select id="formatBlock" class="adv-option-button">
                <option value="H1">H1</option>
                <option value="H2">H2</option>
                <option value="H3">H3</option>
                <option value="H4">H4</option>
                <option value="H5">H5</option>
                <option value="H6">H6</option>
            </select>
            <!-- Font -->
            <select id="fontName" class="adv-option-button"></select>
            <select id="fontSize" class="adv-option-button"></select>
            <!-- Color -->
            <div class="input-wrapper">
                <input type="color" id="foreColor" class="adv-option-button" />
                <label for="foreColor">Font Color</label>
            </div>
            <div class="input-wrapper">
                <input type="color" id="backColor" class="adv-option-button" />
                <label for="backColor">Highlight Color</label>
            </div>
        </div>
        @if(@isset($update))
            {{ html()->form('PUT', url('admin/updates', $update->id))->open() }}
        @else 
            {{ html()->form('POST', url('admin/updates'))->open() }}
        @endif
        <div style="text-align:right;">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" @if(isset($update)) value="{{ $update->title }}" @endif>

            <label for="ver">Version:</label>
            <input type="text" id="ver" name="version" @if(isset($update))value="{{ $update->version }}" @endif>

            <label for="writer">Writer:</label>
            <input type="text" id="writer" name="writer" @if(isset($update)) value="{{ $update->writer }}" @endif>

        </div><br>

        <div style="text-align:right;">
            <label for="visible">Visible:</label>
            <input type="checkbox" id="visible" name="is_visible"
                @if(isset($update) and ($update->is_visible)) checked @endif />
        </div>

        <label>Enter description here:</label>
        <div id="text-input" contenteditable="true" name="text"></div><br>
        <input type="hidden" id="hiddeninput" name="description" />

        <input class="btn btn-info" id="save" type="submit" value="Save" name="submit" />
        {{ html()->form()->close() }}
    </div>
</div>