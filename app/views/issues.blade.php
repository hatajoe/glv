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
        <h3 class="box-title">Issues</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
            <?php $i = 0; ?>
            @foreach ($members as $m)
                @if ($i == 0)
                    <li class="active"><a href="#user-{{ $m->user['id'] }}" data-toggle="tab">{{ $m->user['name'] }}</a></li>
                @else
                    <li><a href="#user-{{ $m->user['id'] }}" data-toggle="tab">{{ $m->user['name'] }}</a></li>
                @endif
                <?php ++$i; ?>
            @endforeach
            </ul>
        </div>
        <?php $i = 0; ?>
        <div class="tab-content">
            @foreach ($members as $m)
                @if ($i == 0)
                    <div class="tab-pane active" id="user-{{ $m->user['id'] }}">
                @else
                    <div class="tab-pane" id="user-{{ $m->user['id'] }}">
                @endif
                <?php ++$i; ?>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>title</th>
                                <th>state</th>
                                <th>milestone</th>
                                <th>created_at</th>
                                <th>updated_at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($m->issues as $i)
                                <tr>
                                    <th>{{ $i['id'] }}</th>
                                    <th><a href='{{ $i["web_url"] }}' target=”_blank”>{{ $i['title'] }}</a></th>
                                    <th>{{ $i['state'] }}</th>
                                    <th>{{ $i['milestone']['title'] }}</th>
                                    <th>{{ $i['created_at'] }}</th>
                                    <th>{{ $i['updated_at'] }}</th>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>id</th>
                                <th>description</th>
                                <th>state</th>
                                <th>milestone</th>
                                <th>created_at</th>
                                <th>updated_at</th>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            @endforeach
        </div><!-- /.tab-content -->
    </div><!-- /.box-body -->
</div><!-- /.box -->
</sction>
@stop
@section('js')
<script type="text/javascript">
$(function() {
    $("#example1").dataTable();
    $('#example2').dataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": false,
        "bSort": true,
        "bInfo": true,
        "bAutoWidth": false
    });
});
</script>
@stop
