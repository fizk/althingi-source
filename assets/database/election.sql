-- -----------------------------------------------------
-- Table `althingi`.`ElectionResult`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ElectionResult` ;
DROP TABLE IF EXISTS `Election_has_Assembly` ;
DROP TABLE IF EXISTS `Election` ;


CREATE TABLE IF NOT EXISTS `althingi`.`Election` (
  `election_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `title` VARCHAR(255) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`election_id`))
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `ElectionResult` (
  `election_result_id` INT NOT NULL AUTO_INCREMENT,
  `election_id` INT NOT NULL,
  `party_id` INT NOT NULL,
  `seat` INT NULL,
  `result` DECIMAL(5,2) NULL,
  PRIMARY KEY (`election_result_id`),
  INDEX `fk_ElectionResult_Election1_idx` (`election_id` ASC),
  INDEX `fk_ElectionResult_Party1_idx` (`party_id` ASC),
  CONSTRAINT `fk_ElectionResult_Election1`
  FOREIGN KEY (`election_id`)
  REFERENCES `althingi`.`Election` (`election_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ElectionResult_Party1`
  FOREIGN KEY (`party_id`)
  REFERENCES `althingi`.`Party` (`party_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `Election_has_Assembly` (
  `election_id` INT NOT NULL,
  `assembly_id` INT NOT NULL,
  PRIMARY KEY (`election_id`, `assembly_id`),
  INDEX `fk_Election_has_Election_Election1_idx` (`election_id` ASC),
  INDEX `fk_Election_has_Election_Assembly1_idx` (`assembly_id` ASC),
  CONSTRAINT `fk_Election_has_Election_Election1`
  FOREIGN KEY (`election_id`)
  REFERENCES `althingi`.`Election` (`election_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Election_has_Election_Assembly1`
  FOREIGN KEY (`assembly_id`)
  REFERENCES `althingi`.`Assembly` (`assembly_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;


insert into `Election` (`election_id`, `date`, `title`, `description`) values
  ( 1, '1963-06-09', 'Alþingiskosningar 1963', 'Alþingiskosningar 1963 voru kosningar til Alþingis Íslands sem haldnar voru 9. júní 1963. Kosningaþátttaka var 91,1%. Í kosningunum hélt Viðreisnarstjórn Sjálfstæðisflokks og Alþýðuflokks velli með 32ja þingmanna meirihluta. 14. nóvember tók Bjarni Benediktsson við forsætisráðuneytinu af Ólafi Thors og Jóhann Hafstein tók við ráðuneytum Bjarna: dóms- og kirkjumálaráðuneyti, iðnaðarráðuneyti og heilbrigðisráðuneyti.'),
  ( 2, '1967-06-11', 'Alþingiskosningar 1967', 'Alþingiskosningar 1967 voru kosningar til Alþingis haldnar 11. júní 1967. Niðurstöður kosninganna voru þær að Viðreisnarstjórn Alþýðuflokks og Sjálfstæðisflokks hélt velli með 32 þingmanna meirihluta. Kosningaþátttaka var 91,4%.'),
  ( 3, '1971-06-13', 'Alþingiskosningar 1971', 'Alþingiskosningar 1971 voru kosningar til Alþingis sem fóru fram 13. júní 1971. Kosningaþátttaka var 90,4%. Þau stórtíðindi urðu að Viðreisnarstjórn Alþýðuflokks og Sjálfstæðisflokks féll eftir tólf ára setu með þriggja þingmanna mun. Alþýðuflokkurinn fékk mikinn skell og missti þriðjung þingmanna sinna. Í þessum kosningum buðu fram Samtök frjálslyndra og vinstri manna, klofningsframboð Hannibals Valdimarssonar vegna andstöðu við að Alþýðubandalaginu yrði breytt úr kosningabandalagi í stjórnmálaflokk. Samtökin fengu fimm þingmenn. Framboðsflokkurinn, háðframboð ungs fólks sem gerði grín að hinum framboðunum og lagði fram stefnumál sem voru út í hött, fékk um 2% fylgi á landsvísu en það bauð fram í þremur kjördæmum. Eitt af slagorðum framboðsins var „jarðgöng í hvern hrepp!“. Meðal frambjóðenda voru Gunnlaugur Ástgeirsson, bróðir Kristínar Ástgeirsdóttur sem síðar sat á þingi fyrir Kvennalistann, Ásta Ragnheiður Jóhannesdóttir, síðar þingkona fyrir Þjóðvaka og Samfylkinguna, og Jörmundur Ingi Hansen, síðar allsherjargoði'),
  ( 4, '1974-06-30', 'Alþingiskosningar 1974', 'Að loknum kosningum mynduðu Sjálfstæðisflokkur og Framsóknarflokkur ríkisstjórn undir forystu Geirs Hallgrímssonar.'),
  ( 5, '1978-06-25', 'Alþingiskosningar 1978', 'Í kjölfar kosninganna mynduðu Framsóknarflokkur, Alþýðuflokkur og Alþýðubandalag ríkisstjórn undir forsæti Ólafs Jóhannessonar, Framsóknarflokki.'),
  ( 6, '1979-12-02', 'Alþingiskosningar 1979', 'Alþingiskosningar 1979 voru kosningar til Alþingis sem fóru fram 2.-3. desember 1979. Þá hafði starfsstjórn Alþýðuflokks undir forsæti Benedikts Gröndals setið í tæpa þrjá mánuði með hlutleysi Sjálfstæðisflokksins eftir að önnur ríkisstjórn Ólafs Jóhannessonar hafði sprungið fyrir tilverknað Alþýðuflokksins, meðal annars vegna deilna ráðherra Alþýðuflokks og Alþýðubandalags og óstjórnar í efnahagsmálum. Framsóknarflokkurinn vann góðan kosningasigur með nýjum formanni Steingrími Hermannssyni og fékk 17 þingmenn. Hann bætti þar með upp tapið frá því í kosningunum árið áður þegar hann hafði aðeins fengið 12. Eftir kosningarnar tóku stjórnarmyndunarviðræður mjög langan tíma og undir lok janúar 1980 hóf forsetinn Kristján Eldjárn að vinna drög að utanþingsstjórn. Þá tók Gunnar Thoroddsen af skarið og myndaði ríkisstjórn með Framsóknarflokki og Alþýðubandalagi með stuðning hluta þingflokks Sjálfstæðismanna en gegn vilja formanns flokksins Geirs Hallgrímssonar.'),
  ( 7, '1983-04-23', 'Alþingiskosningar 1983', ''),
  ( 8, '1987-04-25', 'Alþingiskosningar 1987', 'Alþingiskosningar 1987 voru kosningar til Alþingis sem fram fóru 25. apríl 1987. Á kjörskrá voru 171.402 og kosningaþátttaka var 90,1%. Nýr flokkur Alberts Guðmundssonar, Borgaraflokkurinn, bauð fram í fyrsta skipti og fékk sjö þingmenn en Sjálfstæðisflokkurinn tapaði fimm. Kvennalistinn tvöfaldaði fylgi sitt og fékk sex þingmenn. Stjórn Framsóknarflokks og Sjálfstæðisflokks tapaði þingmeirihluta sínum en Þorsteinn Pálsson myndaði þá ríkisstjórn með Framsóknarflokki og Alþýðuflokki. Steingrímur Hermannsson var utanríkisráðherra og Jón Baldvin Hannibalsson fjármálaráðherra. Þessi ríkisstjórn hélt velli í rúmt ár og var gagnrýnd meðal annars fyrir að ná ekki samkomulagi um brýnar aðgerðir í efnahagsmálum. Hún sprakk, að sagt er, í beinni útsendingu á Stöð 2, en eftir það myndaði Steingrímur Hermannsson vinstristjórn með stuðningi Alþýðuflokks og Alþýðubandalags og síðar Borgaraflokksins, en Sjálfstæðismenn fóru í stjórnarandstöðu.'),
  ( 9, '1991-04-20', 'Alþingiskosningar 1991', 'Alþingiskosningar 1991 voru kosningar til Alþingis Íslendinga sem fóru fram 20. apríl 1991. Á kjörskrá voru 182.768 manns. Kosningaþátttaka var 87,6%. Vinstristjórn Steingríms Hermannssonar hélt velli og Framsóknarflokkur og Alþýðubandalag vildu halda samstarfinu áfram en ekki reyndist vilji til þess meðal forystu Alþýðuflokksins. Eftir kosningarnar fór Davíð Oddsson með stjórnarmyndunarumboð fyrir Sjálfstæðisflokkinn og myndaði stjórn með Alþýðuflokki. Þessi fyrsta ríkisstjórn Davíðs Oddssonar var kölluð Viðeyjarstjórnin af því að hún var mynduð í Viðeyjarstofu en þangað bauð Davíð, sem þá var enn borgarstjóri í Reykjavík, Jóni Baldvin Hannibalssyni til stjórnarmyndunarviðræðna.'),
  (10, '1995-04-08', 'Alþingiskosningar 1995', 'Alþingiskosningar 1995 voru kosningar til Alþingis Íslendinga sem fóru fram 8. apríl 1995. Á kjörskrá voru 191.973 manns. Kosningaþátttaka var 87,4%. Í þessum kosningum bauð fram nýtt stjórnmálaafl, Þjóðvaki, klofningsframboð úr Alþýðuflokknum undir stjórn Jóhönnu Sigurðardóttur og Ágústs Einarssonar, fékk fjóra þingmenn en Alþýðuflokkurinn missti þrjá. Þrír þessara Þjóðvakaþingmanna gengu síðar á kjörtímabilinu í „þingflokk jafnaðarmanna“ ásamt þingmönnum Alþýðuflokksins. Kvennalistinn fékk ekki nema þrjár þingkonur - missti tvær frá fyrra kjörtímabili.'),
  (11, '1999-03-08', 'Alþingiskosningar 1999', 'Alþingiskosningarnar 1999 fóru fram 8. maí. Á kjörskrá voru 201.408 en kjörsókn var 84,1%. Ríkisstjórn Sjálfstæðisflokks og Framsóknarflokks hélt öruggum þingmeirihluta, Framsókn tapaði þremur mönnum en Sjálfstæðismenn bættu við sig einum en fylgi þeirra hafði ekki verið meira síðan í kosningunum 1974. Þessar kosningar voru þær fyrstu eftir verulega breytta flokkaskipun í íslenskum stjórnmálum. Alþýðuflokkurinn, Alþýðubandalagið, Kvennalistinn og Þjóðvaki voru nú ekki lengur í framboði heldur ákváðu þessir flokkar að bjóða fram saman undir merkjum Samfylkingar í því sjónarmiði að búa til breiðfylkingu jafnaðarmanna og félagshyggjufólks. Nokkrir þingmenn og varaþingmenn Alþýðubandalagsins gátu þó ekki hugsað sér að taka þátt í slíku og stofnuðu nýjan vinstri flokk: Vinstri hreyfinguna – grænt framboð. Þriðji nýji flokkurinn sem lét að sér kveða var svo Frjálslyndi flokkurinn undir stjórn Sverris Hermannssonar sem stofnaður var haustið 1998 og lagði mikla áherslu á málefni fiskveiðistjórnunar. 17,7% fylgi á Vestfjörðum tryggði flokknum tvo menn inn á þing.'),
  (12, '2003-03-10', 'Alþingiskosningar 2003', 'Alþingiskosningarnar 2003 fóru fram þann 10. maí. Í fyrsta skipti var kosið samkvæmt nýrri kjördæmaskipan. Á kjörskrá voru 211.304 en kosningaþátttaka var 87,7%. Stjórn Framsóknar- og Sjálfstæðisflokks undir forystu Davíðs Oddssonar hélt þingmeirihluta sínum þrátt fyrir að Sjálfstæðisflokkurinn tapaði fjórum sætum. Samfylkingin bætti við sig þremur sætum, Frjálslyndi flokkurinn tveimur, Vinstri-grænir töpuðu einum manni og Framsóknarflokkurinn stóð í stað.'),
  (13, '2007-03-02', 'Alþingiskosningar 2007', 'Alþingiskosningarnar 2007 voru haldnar laugardaginn 12. maí 2007. Á kjörskrá voru 221.368 og kjörsókn var 83,6%. Stjórn Framsóknarflokks og Sjálfstæðisflokks hélt eins sætis þingmeirihluta en hafði áður þriggja sæta meirihluta. Framsóknarflokkur fékk lægsta fylgið í sögu flokksins en Samfylkingin tapaði einnig fylgi. Sjálfstæðisflokkurinn bætti við sig þremur mönnum en Vinstri græn unnu stærsta sigurinn og bættu við sig fjórum mönnum. Frjálslyndi flokkurinn fékk ríflega 7% fylgi og hélt fjórum þingmönnum. Tvö ný flokksframboð voru tilkynnt fyrir kosningarnar. Annars vegar Baráttusamtaka eldri borgara og öryrkja, sem unnu að hagsmunum eldri borgara og öryrkja. Tíu dögum fyrir kosningarnar tilkynntu Baráttusamtökin um að þau hefðu hætt við framboð, en framboðslistar þeirra höfðu ekki borist í tíma nema í Norðausturkjördæmi. Hitt nýja framboðið var Íslandshreyfingin, flokkur með Margréti Sverrisdóttur og Ómar Ragnarsson í fararbroddi með áherslu á umhverfismál. Hún hlaut 3,3% atkvæða á landsvísu en náði ekki inn manni. Ríkisstjórnin hélt því ekki velli nema fram í janúar 2009, í tæp tvö ár. Þá var fyrsta ríkisstjórn Jóhönnu Sigurðardóttur mynduð fram að því að alþingiskosningar 2009 voru haldnar þann 25. apríl sama ár.'),
  (14, '2009-04-25', 'Alþingiskosningar 2009', 'Alþingiskosningar voru haldnar 25. apríl 2009. Á kjörskrá voru 227.896 kjósendur, þar af konur 114.295 en karlar 113.601. Atkvæði greiddu 193.934 og var kjörsókn því 85,1%. Samfylkingin fékk flest atkvæði 29,8% og 20 þingmenn. Sjálfstæðisflokkurinn hefur aldrei fengið minna kjörfylgi í sögu sinni 23,7%, tapaði 12,9% frá fyrri kosningum og 10 þingmönnum, er nú með 16 þingmenn. Framsóknarflokkurinn fékk 14,8% fylgi - nokkru betra fylgi en skoðanakannanir höfðu spáð - og bættu við sig tveimur þingmönnum, hafa nú níu þingmenn. Vinstrihreyfingin – grænt framboð fékk 21,7% atkvæða, bætti við sig fimm þingmönnum og er nú með 14 þingmenn er það mesta fylgi við flokkinn í tíu ára sögu hans. Frjálslyndi flokkurinn fékk eingöngu 2,2% fylgi en það dugði ekki til að fá þingmann inn, flokkurinn er því utan þings. Borgarahreyfingin sem var stofnuð stuttu fyrir kosningarnar fékk fjóra þingmenn kosna eða 7,2% fylgi. Lýðræðishreyfingin fékk 0,6% og virðist ekki hafa verið tekin alvarlega af kjósendum. Nokkur umræða hefur verið um að þrír stjórnmálaflokkar, sem saman mynda meirihluta hafa lýst því yfir að þeir styðji samningsviðræður um inngöngu Íslands í Evrópusambandið. Kosningarnar voru fyrir margra hluta sakir sögulegar. Af 63 þingmönnum voru 27 nýir þingmenn kosnir til Alþingis og er það mesta endurnýjun á milli kosninga í sögu íslenska lýðveldisins. Konur á Alþingi eru 26 talsins eða 43% og hefur kynjahlutfallið aldrei verið jafnara. Átta þingmenn, sem sóttust eftir endurkjöri og voru ofarlega á framboðslistum, náðu ekki endurkjöri. Aldrei hafa fleiri skilað auðu á kosningum eða 3,2%. Samkvæmt skoðanakönnun sem unnin var af Gallup gerði ríflega fjórðungur kjósenda upp hug sinn um hvað þeir ætluðu að kjósa á kjördeginum sjálfum. Vegna aðildar að Öryggis- og samvinnustofnun Evrópu (ÖSE) var eftirlitsmönnum frá stofnuninni boðið af íslensku fastanefndinni við hana að hafa eftirlit með kosningum á Íslandi. Skömmu seinna ákvað ÖSE að rétt væri að stofnuninn sendi eftirlitsmenn og er það í fyrsta skipti sem ÖSE hefur eftirlit með kosningum á Íslandi. Í tengslum við kosningarnar á sérstaklega að gefa gaum að kosningalöggjöfinni og hugsanlegum breytingum á henni, utankjörfundaratkvæðagreiðslu, fjölmiðlamálum og aðgangi eftirlitsmanna.[10] Tíu starfsmenn á vegum ÖSE munu starfa hér í því augnamiði og skila af sér skýrslu að kosningunum loknum'),
  (15, '2013-04-27', 'Alþingiskosningar 2013', 'Alþingiskosningar 2013 voru haldnar 27. apríl 2013. Þær voru 21. kosningarnar til Alþingis frá lýðveldisstofnun. Til viðbótar við þá fjóru flokka sem sögulega hafa myndað það sem kallað hefur verið íslenska fjórflokkakerfið, boðuðu 11 ný framboð þátttöku. Framboðin voru því 15 í allt og hafa aldrei verið fleiri í alþingiskosningum en 11 þeirra buðu fram í öllum kjördæmum. Undanliðið kjörtímabil einkenndist af hörðum deilum um mörg stór mál á borð við aðildarumsókn Íslands að Evrópusambandinu, Icesave, stjórnarskrármál og breytingar á fiskveiðistjórnunarkerfinu. Í ýmsum málum var ekki samstaða innan stjórnarmeirihlutans á þingi og gengu nokkrir þingmenn úr þingflokkum stjórnarflokkanna þannig að síðustu mánuði sína í embætti var stjórnin í raun minnihlutastjórn sem naut hlutleysis óflokksbundinna þingmanna. Sú staða er óvenjuleg í íslenskum stjórnmálum en mikil hefð er fyrir sterkum meirihlutastjórnum.'),
  (16, '2016-10-29', 'Alþingiskosningar 2016', 'Alþingiskosningar voru haldnar á Íslandi laugardaginn 29. október 2016, í 22. skiptið sem þær hafa verið haldnar frá lýðveldisstofnun. Kosningar voru síðast haldnar vorið 2013 og voru því ekki á dagskrá fyrr enn í síðasta lagi 22. apríl 2017. Átök og stjórnarkreppa vegna leka á gögnum í skattaskjólum sem kölluð hafa verið panamaskjölin sem vörpuðu ljósi á eigur íslenskra ráðamanna í slíkum skjólum urðu til þess að Sigmundur Davíð Gunnlaugsson sagði af sér sem forsætisráðherra og þingkosningum var flýtt til haustsins 2016. 22 þingmenn ákváðu að hætta á þingi eða urðu undir í prófkjörum. ');

insert into `ElectionResult` (`election_id`, `party_id`, `seat`) values
  ( 1, 27,  8),
  ( 1,  2, 19),
  ( 1, 35, 24),
  ( 1, 21,  9),

  ( 2, 27,  9),
  ( 2,  2, 18),
  ( 2, 35, 23),
  ( 2, 21, 10),

  ( 3, 27,  6),
  ( 3,  2, 17),
  ( 3, 35, 22),
  ( 3, 21, 10),
  ( 3, 36,  5),

  ( 4, 27,  5),
  ( 4,  2, 17),
  ( 4, 35, 25),
  ( 4, 21, 11),
  ( 4, 36,  2),

  ( 5, 27, 14),
  ( 5, 2,	12),
  ( 5, 35, 20),
  ( 5, 21, 14),

  ( 6, 27, 10),
  ( 6,  2, 17),
  ( 6, 35, 21),
  ( 6, 21, 11),
  ( 6, 17, 1),

  ( 7, 27,  6),
  ( 7,  2, 14),
  ( 7, 35, 23),
  ( 7, 21, 10),
  ( 7, 31,  4),
  ( 7, 39,  3),

  ( 8, 27, 10	),
  ( 8,  2, 13	),
  ( 8, 35, 18	),
  ( 8, 21,  8	),
  ( 8, 39,  6	),
  ( 8, 33,  7	),
  ( 8, 28,  1	),

  ( 9, 27, 10),
  ( 9,  2, 13),
  ( 9, 35, 26),
  ( 9, 21,  9),
  ( 9, 39,  5),

  (10, 27,  7),
  (10,  2, 15),
  (10, 35, 25),
  (10, 21,  9),
  (10, 39,  3),
  (10, 12,  4),

  (11,  2, 12),
  (11, 35, 26),
  (11, 32,  2),
  (11, 38, 17),
  (11, 23,  6),

  (12,  2, 12),
  (12, 35, 22),
  (12, 32,  4),
  (12, 38, 20),
  (12, 23,  5),

  (13,  2,  7),
  (13, 35, 25),
  (13, 32,  4),
  (13, 38, 18),
  (13, 23,  9),

  (14, 2,	  9),
  (14, 35, 16),
  (14, 41,  4),
  (14, 38, 20),
  (14, 23, 14),

  (15,  2, 19),
  (15, 35, 19),
  (15, 38,  9),
  (15, 23,  7),
  (15, 44,  6),
  (15, 43,  3);



insert into `Election_has_Assembly` (`election_id`, `assembly_id`) values
  ( 1, 84),
  ( 1, 85),
  ( 1, 86),
  ( 1, 87),

  ( 2, 88),
  ( 2, 89),
  ( 2, 90),
  ( 2, 91),


  ( 3, 92),
  ( 3, 93),
  ( 3, 94),

  ( 4, 95),
  ( 4, 96),
  ( 4, 97),
  ( 4, 98),
  ( 4, 99),

  ( 5, 100),
  ( 5, 101),

  ( 6, 102),
  ( 6, 103),
  ( 6, 104),
  ( 6, 105),

  ( 7, 106),
  ( 7, 107),
  ( 7, 108),
  ( 7, 109),

  ( 8, 110),
  ( 8, 111),
  ( 8, 112),
  ( 8, 113),

  ( 9, 114),
  ( 9, 115),
  ( 9, 116),
  ( 9, 117),
  ( 9, 118),

  (10, 119),
  (10, 120),
  (10, 121),
  (10, 122),
  (10, 123),

  (11, 124),
  (11, 125),
  (11, 126),
  (11, 127),
  (11, 128),

  (12, 129),
  (12, 130),
  (12, 131),
  (12, 132),
  (12, 133),

  (13, 134),
  (13, 135),
  (13, 136),

  (14, 137),
  (14, 138),
  (14, 139),
  (14, 140),
  (14, 141),

  (15, 142),
  (15, 143),
  (15, 144),
  (15, 145);




update `ElectionResult` set `result` = 14.2 where `election_id` = 1 and party_id = 27;
update `ElectionResult` set `result` = 15.7 where `election_id` = 2 and party_id = 27;
update `ElectionResult` set `result` = 10.5 where `election_id` = 3 and party_id = 27;
update `ElectionResult` set `result` = 9.1 where `election_id` = 4 and party_id = 27;
update `ElectionResult` set `result` = 22 where `election_id` = 5 and party_id = 27;
update `ElectionResult` set `result` = 17.4 where `election_id` = 6 and party_id = 27;
update `ElectionResult` set `result` = 11.7 where `election_id` = 7 and party_id = 27;
update `ElectionResult` set `result` = 15.2 where `election_id` = 8 and party_id = 27;
update `ElectionResult` set `result` = 15.5 where `election_id` = 9 and party_id = 27;
update `ElectionResult` set `result` = 11.4 where `election_id` = 10 and party_id = 27;
update `ElectionResult` set `result` = 28.2 where `election_id` = 1 and party_id = 2;
update `ElectionResult` set `result` = 28.1 where `election_id` = 2 and party_id = 2;
update `ElectionResult` set `result` = 25.3 where `election_id` = 3 and party_id = 2;
update `ElectionResult` set `result` = 24.9 where `election_id` = 4 and party_id = 2;
update `ElectionResult` set `result` = 16.9 where `election_id` = 5 and party_id = 2;
update `ElectionResult` set `result` = 24.9 where `election_id` = 6 and party_id = 2;
update `ElectionResult` set `result` = 19 where `election_id` = 7 and party_id = 2;
update `ElectionResult` set `result` = 18.9 where `election_id` = 8 and party_id = 2;
update `ElectionResult` set `result` = 18.9 where `election_id` = 9 and party_id = 2;
update `ElectionResult` set `result` = 23.3 where `election_id` = 10 and party_id = 2;
update `ElectionResult` set `result` = 18.4 where `election_id` = 11 and party_id = 2;
update `ElectionResult` set `result` = 17.7 where `election_id` = 12 and party_id = 2;
update `ElectionResult` set `result` = 11.7 where `election_id` = 13 and party_id = 2;
update `ElectionResult` set `result` = 14.8 where `election_id` = 14 and party_id = 2;
update `ElectionResult` set `result` = 24.4 where `election_id` = 15 and party_id = 2;
update `ElectionResult` set `result` = 41.4 where `election_id` = 1 and party_id = 35;
update `ElectionResult` set `result` = 37.5 where `election_id` = 2 and party_id = 35;
update `ElectionResult` set `result` = 36.2 where `election_id` = 3 and party_id = 35;
update `ElectionResult` set `result` = 42.7 where `election_id` = 4 and party_id = 35;
update `ElectionResult` set `result` = 32.7 where `election_id` = 5 and party_id = 35;
update `ElectionResult` set `result` = 35.4 where `election_id` = 6 and party_id = 35;
update `ElectionResult` set `result` = 38.7 where `election_id` = 7 and party_id = 35;
update `ElectionResult` set `result` = 27.2 where `election_id` = 8 and party_id = 35;
update `ElectionResult` set `result` = 38.6 where `election_id` = 9 and party_id = 35;
update `ElectionResult` set `result` = 37.1 where `election_id` = 10 and party_id = 35;
update `ElectionResult` set `result` = 40.7 where `election_id` = 11 and party_id = 35;
update `ElectionResult` set `result` = 33.7 where `election_id` = 12 and party_id = 35;
update `ElectionResult` set `result` = 36.6 where `election_id` = 13 and party_id = 35;
update `ElectionResult` set `result` = 23.7 where `election_id` = 14 and party_id = 35;
update `ElectionResult` set `result` = 26.7 where `election_id` = 15 and party_id = 35;
update `ElectionResult` set `result` = 16 where `election_id` = 1 and party_id = 21;
update `ElectionResult` set `result` = 17.6 where `election_id` = 2 and party_id = 21;
update `ElectionResult` set `result` = 17.1 where `election_id` = 3 and party_id = 21;
update `ElectionResult` set `result` = 18.3 where `election_id` = 4 and party_id = 21;
update `ElectionResult` set `result` = 22.9 where `election_id` = 5 and party_id = 21;
update `ElectionResult` set `result` = 19.7 where `election_id` = 6 and party_id = 21;
update `ElectionResult` set `result` = 17.3 where `election_id` = 7 and party_id = 21;
update `ElectionResult` set `result` = 13.3 where `election_id` = 8 and party_id = 21;
update `ElectionResult` set `result` = 14.4 where `election_id` = 9 and party_id = 21;
update `ElectionResult` set `result` = 14.3 where `election_id` = 10 and party_id = 21;
update `ElectionResult` set `result` = 8.9 where `election_id` = 3 and party_id = 36;
update `ElectionResult` set `result` = 4.6 where `election_id` = 4 and party_id = 36;
update `ElectionResult` set `result` = 7.3 where `election_id` = 7 and party_id = 31;
update `ElectionResult` set `result` = 0.2 where `election_id` = 8 and party_id = 31;
update `ElectionResult` set `result` = 5.5 where `election_id` = 7 and party_id = 39;
update `ElectionResult` set `result` = 10.1 where `election_id` = 8 and party_id = 39;
update `ElectionResult` set `result` = 8.3 where `election_id` = 9 and party_id = 39;
update `ElectionResult` set `result` = 4.9 where `election_id` = 10 and party_id = 39;
update `ElectionResult` set `result` = 10.9 where `election_id` = 8 and party_id = 33;
update `ElectionResult` set `result` = 1.2 where `election_id` = 8 and party_id = 28;
update `ElectionResult` set `result` = 7.2 where `election_id` = 10 and party_id = 12;
update `ElectionResult` set `result` = 4.2 where `election_id` = 11 and party_id = 32;
update `ElectionResult` set `result` = 7.4 where `election_id` = 12 and party_id = 32;
update `ElectionResult` set `result` = 7.3 where `election_id` = 13 and party_id = 32;
update `ElectionResult` set `result` = 2.2 where `election_id` = 14 and party_id = 32;
update `ElectionResult` set `result` = 7.2 where `election_id` = 14 and party_id = 41;
update `ElectionResult` set `result` = 26.8 where `election_id` = 11 and party_id = 38;
update `ElectionResult` set `result` = 31 where `election_id` = 12 and party_id = 38;
update `ElectionResult` set `result` = 26.8 where `election_id` = 13 and party_id = 38;
update `ElectionResult` set `result` = 29.8 where `election_id` = 14 and party_id = 38;
update `ElectionResult` set `result` = 12.9 where `election_id` = 15 and party_id = 38;
update `ElectionResult` set `result` = 9.1 where `election_id` = 11 and party_id = 23;
update `ElectionResult` set `result` = 8.8 where `election_id` = 12 and party_id = 23;
update `ElectionResult` set `result` = 14.3 where `election_id` = 13 and party_id = 23;
update `ElectionResult` set `result` = 21.7 where `election_id` = 14 and party_id = 23;
update `ElectionResult` set `result` = 10.9 where `election_id` = 15 and party_id = 23;
update `ElectionResult` set `result` = 8.2 where `election_id` = 15 and party_id = 44;
update `ElectionResult` set `result` = 5.1 where `election_id` = 15 and party_id = 43;
update `ElectionResult` set `result` = 0.2 where `election_id` = 1 and party_id = 17;
update `ElectionResult` set `result` = 1.1 where `election_id` = 2 and party_id = 17;
update `ElectionResult` set `result` = 2 where `election_id` = 3 and party_id = 17;
update `ElectionResult` set `result` = 0.4 where `election_id` = 4 and party_id = 17;
update `ElectionResult` set `result` = 5.5 where `election_id` = 5 and party_id = 17;
update `ElectionResult` set `result` = 2.5 where `election_id` = 6 and party_id = 17;
update `ElectionResult` set `result` = 0.5 where `election_id` = 7 and party_id = 17;
update `ElectionResult` set `result` = 2.9 where `election_id` = 8 and party_id = 17;
update `ElectionResult` set `result` = 4.3 where `election_id` = 9 and party_id = 17;
update `ElectionResult` set `result` = 1.9 where `election_id` = 10 and party_id = 17;
update `ElectionResult` set `result` = 0.8 where `election_id` = 11 and party_id = 17;
update `ElectionResult` set `result` = 1.4 where `election_id` = 12 and party_id = 17;
update `ElectionResult` set `result` = 3.3 where `election_id` = 13 and party_id = 17;
update `ElectionResult` set `result` = 0.6 where `election_id` = 14 and party_id = 17;
update `ElectionResult` set `result` = 11.8 where `election_id` = 15 and party_id = 17;


-- select * from `Assembly` where `from` > '1963-06-09' and `from` < '1967-06-11';
-- select * from `Assembly` where `from` > '1967-06-11' and `from` < '1971-06-13';
-- select * from `Assembly` where `from` > '1971-06-13' and `from` < '1974-06-30';
-- select * from `Assembly` where `from` > '1974-06-30' and `from` < '1978-06-25';
-- select * from `Assembly` where `from` > '1978-06-25' and `from` < '1979-12-02';
-- select * from `Assembly` where `from` > '1979-12-02' and `from` < '1983-04-23';
-- select * from `Assembly` where `from` > '1983-04-23' and `from` < '1987-04-25';
-- select * from `Assembly` where `from` > '1987-04-25' and `from` < '1991-04-20';
-- select * from `Assembly` where `from` > '1991-04-20' and `from` < '1995-04-08';
-- select * from `Assembly` where `from` > '1995-04-08' and `from` < '1999-03-08';
-- select * from `Assembly` where `from` > '1999-03-08' and `from` < '2003-03-10';
-- select * from `Assembly` where `from` > '2003-03-10' and `from` < '2007-03-02';
-- select * from `Assembly` where `from` > '2007-03-02' and `from` < '2009-04-25';
-- select * from `Assembly` where `from` > '2009-04-25' and `from` < '2013-04-27';
-- select * from `Assembly` where `from` > '2013-04-27' and `from` < '2016-10-29';
