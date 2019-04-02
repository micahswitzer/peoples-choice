select e.Project, e.Team, e.Points
from
(
	select p.id as Project, t.id as Team, sum(s.points) as Points
	from mzpc_project as p, mzpc_score as s join mzpc_team as t on s.team_id = t.id
	where t.project_id = p.id
	group by t.id
) as e
group by e.Points desc
limit 3

-- simpler (project specifics) top 3 query
