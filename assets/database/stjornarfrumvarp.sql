update `Issue` I
    join `Document` D on (D.assembly_id = I.assembly_id and D.issue_id = I.issue_id and D.`type` = 'stjórnarfrumvarp')
set I.`type_subname` = 'stjórnarfrumvarp';
