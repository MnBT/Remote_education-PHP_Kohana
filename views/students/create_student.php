<div id="create_grid">
    <form action="" method="POST" id="createStudentForm">
        <div class="grid_area">
            <div>
                <table>
                    <tr><td colspan=3><div class="grid_section_title">Personal data</div></td></tr>
                    <tr><td colspan="3">
                            <label class="grid_title">Primary E-mail: </label><input type="text" name="primaryEmail"/>
                        </td></tr>
                    <tr>
                        <td colspan="3">
                            <div class="grid_title">Name</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="firstname" />
                            <div><label class="grid_signature">First *</label></div>
                        </td>
                        <td>
                            <input type="text" name="lastname" />
                            <div><label class="grid_signature">Last *</label></div>
                        </td>
                        <td>
                            <input type="text" name="middlename" />
                            <div><label class="grid_signature">Middle *</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td><div class="grid_title">Other Names</div></td>
                        <td><div class="grid_title">Birthplace</div></td>
                    </tr>
                    <tr>
                        <td>
                            <div><input type="text" name="othernames" /></div>
                        </td>
                        <td>
                            <select style="width:150px" name="birthplace_country">
                                <?php
                                $orm_countrys = ORM::factory("country");
                                $orm_countrys = $orm_countrys->find_all();
                                foreach ($orm_countrys as $orm_country) {
                                    echo '<option value=' . $orm_country->id . '>' . $orm_country->name . '</option>';
                                }
                                ?>
                            </select>
                            <div><label class="grid_signature">Country *</label></div>
                        </td>
                        <td>
                            <input type="text" name="birthplace_city" />
                            <div><label class="grid_signature">City *</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td><div class="grid_title">Gender *</div></td>
                        <td><div class="grid_title">Birth date *</div></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" value="m" checked name="sex"/>Male
                            <input type="radio" value="f" name="sex"/>Female
                        </td>
                        <td><input type="text" name="birthdate" /></td>
                    </tr>
                    <tr><td colspan=3>
                            <div class="grid_title">Program of Study *</div>
                            <?php
                            $orm_speciality = ORM::factory("speciality");
                            $orm_speciality = $orm_speciality->find_all();
                            foreach ($orm_speciality as $orm_spec) {
                                echo '<div><input type="radio" name="programOfStudy" value=' . $orm_spec->id . '>' . $orm_spec->name . '</div>';
                            }
                            ?>
                        </td></tr>
                    <tr>
                        <td colspan=3><div class="grid_title">Language of Study *</div></td>
                    </tr>	
                    <tr>
                        <td colspan=3>
                            <input type="radio" checked name="languageOfStudy" value="2"/>Русский/Russian
                            <input type="radio" name="languageOfStudy" value="1"/>Английский/English
                        </td>
                    </tr>
                </table>
            </div>
            <div>
                <table>
                    <tr><td colspan="3"><div class="grid_section_title">Job data</div></td></tr>
                    <tr><td colspan=3><div class="grid_title">Are you currently employed?</div></td></tr>
                    <tr>
                        <td colspan=3>
                            <input type="radio" checked name="currEmployed" value="0"/>Yes
                            <input type="radio" name="currEmployed" value="1"/>No
                        </td>
                    </tr>
                    <tr>
                        <td><div class="grid_title">Employer name</div></td>
                        <td><div class="grid_title">Job Title</div></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="currentEmpName"/></td>
                        <td><input type="text" name="currentJobTitle"/></td>
                    </tr>
                    <tr>
                        <td><div class="grid_title">Employed from</div></td>
                        <td><div class="grid_title">Employed to</div></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="currentEmpFrom"/></td>
                        <td><input type="text" name="currentJobTo"/></td>
                    </tr>
                    <tr>
                        <td><div class="grid_title">City</div></td>
                        <td><div class="grid_title">Country</div></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="currentEmpCity"/></td>
                        <td><select style="width:150px" name="currentEmpCountry">
                                <?php
                                $orm_countrys = ORM::factory("country");
                                $orm_countrys = $orm_countrys->find_all();
                                foreach ($orm_countrys as $orm_country) {
                                    echo '<option value=' . $orm_country->id . '>' . $orm_country->name . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="3"><div class="grid_title">Job description</div></td></tr>
                    <tr><td colspan="3"><textarea cols="40" rows="3" name="currentEmpDesc"></textarea></td></tr>
                    <tr><td colspan="3"><div class="grid_title">Comments</div></td></tr>
                    <tr><td colspan="3"><textarea cols="40" rows="3" name="currentEmpComments"></textarea></td></tr>
                </table>
            </div>
        </div>
        <div class="grid_area">
            <div>
                <table>
                    <tr><td colspan=4><div class="grid_section_title">Contacy information</div></td></tr>
                    <tr>
                        <td><div class="grid_title">Telephone (landline)</div></td>
                        <td><div class="grid_title">Mobile phone *</div></td>
                        <td><div class="grid_title">Fax</div></td>
                        <td><div class="grid_title">Additional Email</div></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="telephone" /></td>
                        <td><input type="text" name="mobilephone" /></td>
                        <td><input type="text" name="fax" /></td>
                        <td><input type="text" name="additEmail" /></td>
                    </tr>
                    <tr><td colspan=2><div class="grid_title">Correspondence address</div></td></tr>
                    <tr>
                        <td colspan=2>
                            <input type="text" name="correspondAddress" style="width:316px" />
                            <div><label class="grid_signature">Street Address *</label></div>
                        </td>
                        <td>
                            <input type="text" name="correspondCity" />
                            <div><label class="grid_signature">City *</label></div>
                        </td>
                        <td>
                            <input type="text" name="correspondState" />
                            <div><label class="grid_signature">State / Province / Region *</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="correspondPostal" />
                            <div><label class="grid_signature">Postal / Zip Code *</label></div>
                        </td>
                        <td>
                            <select style="width:150px" name="correspCountry">
                                <?php
                                $orm_countrys = ORM::factory("country");
                                $orm_countrys = $orm_countrys->find_all();
                                foreach ($orm_countrys as $orm_country) {
                                    echo '<option value=' . $orm_country->id . '>' . $orm_country->name . '</option>';
                                }
                                ?>
                            </select>
                            <div><label class="grid_signature">Country *</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="grid_title">Country of citizenship</div>
                            <input type="text" name="CountryCitizenship" />
                        </td>
                        <td colspan=2>
                            <div class="grid_title">Country of permanent residence</div>
                            <select style="width:150px" name="CountryPermanentResidence">
                                <?php
                                $orm_countrys = ORM::factory("country");
                                $orm_countrys = $orm_countrys->find_all();
                                foreach ($orm_countrys as $orm_country) {
                                    echo '<option value=' . $orm_country->id . '>' . $orm_country->name . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan=2><div class="grid_title">Permanent address (if different)</div></td></tr>
                    <tr>
                        <td colspan=2>
                            <input type="text" name="permanentAddress" style="width:316px" />
                            <div><label class="grid_signature">Street Address</label></div>
                        </td>
                        <td>
                            <input type="text" name="permanentCity" />
                            <div><label class="grid_signature">City</label></div>
                        </td>
                        <td>
                            <input type="text" name="permanentState" />
                            <div><label class="grid_signature">State / Province / Region</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="permanentPostal" />
                            <div><label class="grid_signature">Postal / Zip Code</label></div>
                        </td>
                        <td>
                            <select style="width:150px" name="permanentCountry">
                                <?php
                                $orm_countrys = ORM::factory("country");
                                $orm_countrys = $orm_countrys->find_all();
                                foreach ($orm_countrys as $orm_country) {
                                    echo '<option value=' . $orm_country->id . '>' . $orm_country->name . '</option>';
                                }
                                ?>
                            </select>
                            <div><label class="grid_signature">Country</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td><div class="grid_title">Emergency contact</div></td>
                    </tr>
                </table>
            </div>
            <div>
                <table>
                    <tr><td><div class="grid_section_title">Study</div></td></tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <div class="grid_title">Is English your native language</div>
                            <div>
                                <input type="radio" name="notiveLang" value="1" checked />Yes
                                <input type="radio" name="notiveLang" value="0" />No
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <div class="grid_title">If English is not your first language, when has been your language of instruction ?</div>
                            <div>
                                <input type="text" name="firstLanguage" style="width:316px" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <div class="grid_title">Please give the result of any language tests taken (e.g. IELTS, TOEFL, TWE)</div>
                            <div>
                                <input type="text" name="languageResult" style="width:316px" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <div class="grid_title">You are currently a student ?</div>
                            <div>
                                <input type="radio" name="currentStudy" value="1" checked />Yes
                                <input type="radio" name="currentStudy" value="0"/>No
                            </div>
                        </td>
                    </tr>
                    <tr><td><div class="grid_title">Current study</div></td></tr>
                    <tr>
                        <td colspan=2>
                            <div>
                                <input type="text" name="currentStudyName" style="width:316px" />
                                <div><label class="grid_signature">Full name of institution</label></div>
                            </div>
                        </td>
                        <td colspan=2>
                            <div>
                                <input type="text" name="currentStudyLocation" style="width:316px" />
                                <div><label class="grid_signature">Location</label></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="currentStudyEntered" />
                            <div><label class="grid_signature">Entered, Through</label></div>
                        </td>
                        <td>
                            <input type="text" name="currentStudyMajor" />
                            <div><label class="grid_signature">Major or program of study</label></div>
                        </td>
                        <td>
                            <input type="text" name="currentStudyDiplom" />
                            <div><label class="grid_signature">Name of degree or diplome expected</label></div>
                        </td>
                        <td>
                            <input type="text" name="currentStudyExpected" />
                            <div><label class="grid_signature">Date expected</label></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=4>
                            <div class="grid_title">Previous degree-level study (most recent first)</div>
                            <textarea cols="70" rows="5" name="prevDegree"></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <input type="hidden" id="idForBot"/>
        <input type="button" id="saveStuden" class="gridButton" value="Save"/>
        <img src="media/img/del.png" width="16" height="16" id="gridClose" class="gridButton"/>
        <div id="createUser_popup" class="popupclass">
            <a href="#" class="lightbox-close" onclick="$(this).parent().hide('slow'); return false;">
                <span style="display:none;">Close</span>
            </a>
            <div id="form-popup"></div></div>
    </form>
</div>

<script>
    $('#gridClose').bind('click',function(){
        $('#createStudent').css('display','none');
    })
        
    $('#saveStuden').bind('click',function(){
        $.ajax({
            type:'POST',
            url:'/students/save',
            data: $('#createStudentForm').serialize(),
            success: function(response) {
                
                 response = $.parseJSON(response);
                 if (response.success == false) {
                     openPopUp(response);
                 } else {
                     $.ajax({
                        type: 'GET',
                        url: '/students/profile',
                        success: function() {
                            $curretnSelect = response.userId;
                            $.ajax({
                                type: 'POST',
                                url: '/students/select',
                                data: {id:$curretnSelect},
                                success: function (response) {
                                    response = $.parseJSON(response);
                
                                    if (!!response.success && response.content.length > 0)
                                    {
                                        $('#students_main_grid_row_' + selectedUserId).removeClass('current');
                                        $('#students_main_grid_row_' + $curretnSelect).addClass('current');
                                        $('#profiles_grid_row_' + selectedUserId).removeClass('current');
                                        $('#profiles_grid_row_' + $curretnSelect).addClass('current');
                                        $('.students_tabs div.current').removeClass('current');
                                        $('.students_tabs div.menuitem_profile').addClass('current');
                                        $('.students_tab_content').html(response.content);
                                        $('#createStudent').css('display','none');
                                    } else {
                                        alert('<?= __("Content is not loaded") ?>');
                                    }
                                }
                            })
                        }
                    })
                }
            }
        })
    })
    
    $('#student_edit').live('click',function(){
        $.ajax({
            type: 'POST',
            url: '/students/profile',
            success: function() {
                $curretnSelect = $('#student_edit').attr('value');
                $.ajax({
                    type: 'POST',
                    url: '/students/select',
                    data: {id:$curretnSelect},
                    success: function (response) {
                        response = $.parseJSON(response);
                        
                        if (!!response.success && response.content.length > 0)
                        {
                            $('#students_main_grid_row_' + selectedUserId).removeClass('current');
                            $('#students_main_grid_row_' + $curretnSelect).addClass('current');
                            $('#profiles_grid_row_' + selectedUserId).removeClass('current');
                            $('#profiles_grid_row_' + $curretnSelect).addClass('current');
                            $('.students_tabs div.current').removeClass('current');
                            $('.students_tabs div.menuitem_profile').addClass('current');
                            $('.students_tab_content').html(response.content);
                            $('#createUser_popup').css('display','none');
                            $('#createStudent').css('display','none');
                        } else {
                            alert('<?= __("Content is not loaded") ?>');
                        }
                    }
                })
            }
        })
        return false;
    })
    function openPopUp(response) {
    console.log(response);
        if (response.success == false) {
            $('#form-popup').html(
                'Following errors have occured:<div>' + response.errors_mess + '</div>'
            );
        } else {
            $('#form-popup').html(
                '<div>User successfully registered</div>'
            );
        }
        $("#createUser_popup").show("slow");
        return false;
    }
       
</script>