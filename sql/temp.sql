select t.name, max(s.points) as points
from mzpc_team as t join mzpc_score as s on t.id = s.team_id,
(
	select sum(s.points)
    from mzpc_project as p, mzpc_team as t join mzpc_score as s on t.id = s.team_id
    where t.project_id = p.id
    group by t.name
    
) as e
group by t.name

-- it's gross, it's ugly, it's redundant, but boy does it return team names and rank them by max sum of points
