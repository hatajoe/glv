@extends('layouts.master')
@section('content-header')
<h1>
    Issues
</h1>
<ol class="breadcrumb">
    <li><a href="/issues"><i class="fa fa-dashboard"></i> Issues</a></li>
</ol>
@stop
@section('content')
<div class="box">
    <div class="box-header">
        <h3 class="box-title">{{ $user['username'] }}</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table id="example" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>id</th>
                    <th>title</th>
                    <th>state</th>
                    <th>created_at</th>
                    <th>updated_at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($issues as $i)
                    <tr>
                        <th>{{ $i['id'] }}</th>
                        <th><a href='{{ $i["issue_url"] }}' target=”_blank”>{{ $i['title'] }}</a></th>
                        <th>{{ $i['state'] }}</th>
                        <th>{{ $i['created_at'] }}</th>
                        <th>{{ $i['updated_at'] }}</th>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>id</th>
                    <th>title</th>
                    <th>state</th>
                    <th>created_at</th>
                    <th>updated_at</th>
                </tr>
            </tfoot>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
</sction>
@stop
@section('js')
<!-- page script -->
<script type="text/javascript">
$(function() {
    $("#example").dataTable();
});
</script>
@stop
