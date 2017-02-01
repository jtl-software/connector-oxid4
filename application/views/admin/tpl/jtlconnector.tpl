[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<h2>JTL Connector</h2>
<ul class="req">
	<h3>System-Check | Bitte informieren Sie sich über die Mindest-Voraussetzungen und die Installations-Anweisungen im <a href="http://guide.jtl-software.de/jtl/JTL-Connector" style="color: #ff3600;">Connector Guide</a></h3>
    [{$info}]
</ul>
<br>
<ul class="req">
	<h3>Connector-Daten</h3>
	<li>
		<table>
			<tbody>
			<tr>
				<td width="140"><b>Connector URL:</b></td>
				<td>[{$url}]</td>
			</tr>
			<tr>
				<td width="140"><b>Passwort:</b></td>
				<td>[{$pass}]</td>
			</tr>
			<tr>
				<td width="140"><b>Connector-Version:</b></td>
				<td>[{$version}]</td>
			</tr>
			</tbody>
		</table>
	</li>
</ul>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]