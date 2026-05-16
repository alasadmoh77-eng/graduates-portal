<?php
$types = App\Models\DocumentType::all();
if ($types->count() >= 2) {
    App\Models\DocumentRequest::whereNotIn('document_type_id', [$types[0]->id, $types[1]->id])->update(['document_type_id' => $types[0]->id]);
}
