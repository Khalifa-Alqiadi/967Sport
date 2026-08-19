<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    //Relation to webmasterSections
    public function webmasterSection()
    {

        return $this->belongsTo('App\Models\WebmasterSection', 'webmaster_id');
    }

    //Relation of father section
    public function fatherSection()
    {

        return $this->belongsTo('App\Models\Section', 'father_id');
    }

    //Relation of father sections
    public function fatherSections()
    {

        return $this->hasMany('App\Models\Section', 'father_id')->orderby('row_no', 'asc');
    }

    //Relation to Topics
    public function topics()
    {
        return $this->hasMany('App\Models\Topic')->orderby('row_no', 'asc');
    }

    // Topics assigned to this category through the topic_categories pivot table.
    // The CMS supports both this assignment and the legacy topics.section_id field.
    public function categorizedTopics()
    {
        return $this->belongsToMany(
            'App\Models\Topic',
            'topic_categories',
            'section_id',
            'topic_id'
        )->orderby('topics.row_no', 'asc');
    }

    //Relation of TopicCategory
    public function selectedCategories()
    {

        return $this->hasMany('App\Models\TopicCategory', 'section_id');
    }

    public function popup()
    {
        return $this->belongsTo('App\Models\Popup', 'popup_id');
    }
}
