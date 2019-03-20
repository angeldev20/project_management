<div id="row" class="grid">
    <div class="grid__col-sm-12 grid__col-md-3 grid__col-lg-3">
        <div class="list-group">
			<?php foreach ( $submenu as $name => $value ):
				$badge = "";
				$active = "";
				if ( $value == "settings/updates" ) {
					$badge = '<span class="badge badge-success">' . $update_count . '</span>';
				}
				if ( $name == $breadcrumb ) {
					$active = 'active';
				} ?>
                <a style="<?php if($name=="SMTP Settings") echo "display: none;"; ?>" class="list-group-item <?= $active; ?>" id="<?php $val_id = explode( "/", $value );
				if ( ! is_numeric( end( $val_id ) ) ) {
					echo end( $val_id );
				} else {
					$num = count( $val_id ) - 2;
					echo $val_id[ $num ];
				} ?>" href="<?= site_url( $value ); ?>"><?= $badge ?> <?= $name ?></a>
			<?php endforeach; ?>
        </div>
    </div>
    <div class="grid__col-sm-12 grid__col-md-9 grid__col-lg-9">
        <div class="panel">
            <?php //if (!$isSignedUp) : ?>
            <?php
                $attributes = array( 'class' => '', 'id' => 'paypal_existing' );
                echo form_open_multipart( $form_action, $attributes );
            ?>
                <div class="table-head">Spera <?= $this->lang->line( 'application_payments' ); ?> Existing Spera Account</div>
                <div class="panel-body">
                    <p>(previous Spera customers only)</p>
                    <div class="form-group"><label>SPERA ACCOUNT NUMBER</label>
                        <input type="text" name="PropayAccountNumber" class="form-control required" value="<?php if(isset($registerdata)){echo $registerdata['PropayAccountNumber'];}?>"
                            placeholder="account number from www.propay.com"
                        />
                    </div>
                    <div class="form-group">
                        <label>
                            <input class="required" type="checkbox" name="signature" value="1" id="signature">&nbsp;
                            <span>
                                Click here to sign agreeing to the <a target="_blank" href="https://spera.io/payment-services-agreement/">terms and conditions</a>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="panel-footer">
                    <input id="signup_submit" type="submit" name="send" class="btn btn-primary" value="<?= $this->lang->line( 'application_save' ); ?>"/>
                </div>
            <?php echo form_close(); ?>
            <?php //endif; ?>
        </div>
        <div class="panel">
            <div class="table-head">Spera <?= $this->lang->line( 'application_payments' ); ?> <?= $this->lang->line( 'application_settings' ); ?></div>
            <div class="panel-body">
                <?php if ($isSignedUp) : ?>
                    <br> You are signed up for a spera account. You should have received a signup email.
                    <?php if ($isSignedUp->bankingStatus == 0) : ?>
                        <br><br>Remember to setup a Bank Account for you to withdraw funds when receiving money.
                        <br><br>
                        Visit <a target="_blank" href="https://www.propay.com">https://www.propay.com</a>
                    <?php endif; ?>
                <?php else: ?>
                <?php
                    $attributes = array( 'class' => '', 'id' => 'paypal' );
                    echo form_open_multipart( $form_action, $attributes );
                ?>
                <?php if($error != 'false') { ?>
                    <div id="error" style="display:block">
                        <?=$error?>
                    </div>
                <?php } ?>

                <!-- next three inputs need label translations -->
                <style>
                    input[type="number"]::-webkit-outer-spin-button,
                    input[type="number"]::-webkit-inner-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }
                    input[type="number"] {
                        -moz-appearance: textfield;
                    }
                    .option-box {
                        min-height: 50px;
                    }
                </style>
                <div class="form-group"><label>Bank Name</label>
                    <input type="text" name="BankName" class="form-control" value="<?php if(isset($registerdata)){echo $registerdata['BankName'];}?>"
                        placeholder="some bank name (OPTIONAL)"
                    />
                </div>
                <div class="form-group"><label>Routing Number</label>
                    <input type="number" min="10000000" max="999999999" name="RoutingNumber" class="form-control" pattern="[\d]{9}"
                        placeholder="9999999999 (OPTIONAL)"
                        value="<?php if(isset($registerdata)){echo $registerdata['RoutingNumber'];}?>" />
                </div>
                <div class="form-group"><label>Bank Account Number</label>
                    <input type="number" name="BankAccountNumber" class="form-control" pattern="[0-9][0-9]+"
                        placeholder="012345 (OPTIONAL)"
                        value="<?php if(isset($registerdata)){echo $registerdata['BankAccountNumber'];}?>" />
                </div>
                <div class="form-group"><label>Bank Account Type</label>
                    <select name="AccountType" class="formcontrol chosen-select">
                        <option value="C">Checking</option>
                        <option value="S">Savings</option>
                    </select>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_firstname' ) ?></label>
                    <input type="text" name="FirstName" class="required form-control" value="<?php if(isset($registerdata)){echo $registerdata['FirstName'];}?>" required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_lastname' ) ?></label>
                    <input type="text" name="LastName" class="required form-control" value="<?php if(isset($registerdata)){echo $registerdata['LastName'];}?>" required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_address' ) ?></label>
                    <input type="text" name="Address1" class="required form-control" value="<?php if(isset($registerdata)){echo $registerdata['Address1'];}?>" required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_address2' ) ?></label>
                    <input type="text" name="Address2" class="form-control" value="<?php if(isset($registerdata)){echo $registerdata['Address2'];}?>">
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_city' ) ?></label>
                    <input type="text" name="City" class="required form-control" value="<?php if(isset($registerdata)){echo $registerdata['City'];}?>" required/>
                </div>
                <div class="form-group option-box"><label><?= $this->lang->line( 'application_state' ) ?></label>
                    <select name="State" class="formcontrol chosen-select">
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="DC">District Of Columbia</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                    </select>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_zip_code' ) ?></label>
                    <input type="text" name="Zip" class="required form-control"  pattern="[0-9]{5}"
                        value="<?php if(isset($registerdata)){echo $registerdata['Zip'];}?>"
                        maxlength="5"
                        required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_day_phone' ) ?></label>
                    <input type="text" name="DayPhone" class="required form-control" pattern="[0-9][0-9]+"
                        value="<?php if(isset($registerdata)){echo $registerdata['DayPhone'];}?>"
                        maxlength="10"
                        placeholder="1234567890" required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_evening_phone' ) ?></label>
                    <input type="text" name="EveningPhone" class="required form-control"
                        pattern="[0-9][0-9]+"
                        maxlength="10"
                        value="<?php if(isset($registerdata)){echo $registerdata['EveningPhone'];}?>" placeholder="1234567890" required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_date_of_birth' ) ?></label>
                    <input type="text" name="DateOfBirth" class="required form-control"
                        pattern="(?:(?:0[1-9]|1[0-2])[\/\\-. ]?(?:0[1-9]|[12][0-9])|(?:(?:0[13-9]|1[0-2])[\/\\-. ]?30)|(?:(?:0[13578]|1[02])[\/\\-. ]?31))[\/\\-. ]?(?:19|20)[0-9]{2}"
                        value="<?php if(isset($registerdata)){echo $registerdata['DateOfBirth'];}?>"
                        placeholder="The person must be at least 18 years old to obtain an account" placeholder="mm/dd/yyyy" maxlength="10" required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_email' ) ?></label>
                    <input type="email" name="SourceEmail" class="required email form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" value="<?php if(isset($registerdata)){echo $registerdata['SourceEmail'];}?>" required/>
                </div>
                <div class="form-group"><label><?= $this->lang->line( 'application_ssn' ) ?></label>
                    <input type="number" min="100000" max="999999999" name="SocialSecurityNumber" class="required form-control" pattern="[\d]{9}"
                        value="<?php if(isset($registerdata)){echo $registerdata['SocialSecurityNumber'];}?>" placeholder="1234567890"
                        maxlength="9"
                        required/>
                </div>
                <div class="form-group">
                    <label><input class="required" type="checkbox" name="signature" value="1" id="signature"> Click here to sign agreeing to the <a target="_blank" href="https://spera.io/payment-services-agreement/">terms and conditions</a></label>
                </div>
                <div>
                    <input id="signup_submit" type="submit" name="send" class="btn btn-primary"
                        value="<?= $this->lang->line( 'application_save' ); ?>"/>
                </div>
                <?php echo form_close(); ?>
                <?php if (isset($error)) echo $error; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>