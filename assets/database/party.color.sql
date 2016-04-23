update `Party` set `color` = '0000ff' where `name` = 'Sjálfstæðisflokkur';
update `Party` set `color` = '009900' where `name` = 'Framsóknarflokkur';
update `Party` set `color` = 'ff8c00' where `name` = 'Samfylkingin';
update `Party` set `color` = 'ff0000' where `name` = 'Vinstri hreyfingin - grænt framboð';
update `Party` set `color` = '92278f' where `name` = 'Björt framtíð';
update `Party` set `color` = '54306c' where `name` = 'Píratar';


update `Issue` I
    join `Document` D on (D.assembly_id = I.assembly_id and D.issue_id = I.issue_id and D.`type` = 'stjórnarfrumvarp')
set I.`type_subname` = 'stjórnarfrumvarp';
