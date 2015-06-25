{*?template charset=UTF-8*}
<div class="box-header">
    <div class="box-tc">
        <div class="box-ml">
            <div class="box-mr">
                <div class="box-tl">
                    <div class="box-tr">
                        <h4>{'Agenda'|i18n('design/admin/templates/agenda/leftmenu')}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box-bc">
    <div class="box-ml">
        <div class="box-mr">
            <div class="box-bl">
                <div class="box-br">
                    <div class="box-content">
                        <ul>
                            <li><a href={'/agenda/index'|ezurl()}>{'Add an event'|i18n('design/admin/templates/agenda/leftmenu')}</a></li>
                            <li><a href={'/agenda/calendar'|ezurl()}>{'See the calendar'|i18n('design/admin/templates/agenda/leftmenu')}</a></li>
                        </ul>
                        {* DESIGN: Content END *}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

