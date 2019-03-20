<?php

function add_flash_message(array $notification){
    if(empty(session('notification_collection'))) {
        // If notification_collection is either not set or not a collection
        $new_collection = new \Illuminate\Support\Collection();
        $new_collection->push(['notification_message' => $notification['message'], 'notification_type' => $notification['type']]);
        session()->flash('notification_collection', $new_collection);
    } else {
        // Add to the notification-collection
        $notification_collection = \Session::get( 'notification_collection' );
        $notification_collection->push(['notification_message' => $notification['message'],'notification_type' => $notification['type']]);
        session()->flash('notification_collection', $notification_collection);
    }
}
