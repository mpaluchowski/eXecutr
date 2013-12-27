<tr>
    <td colspan="2">
        <label for="repeats-type"><?php echo Base::instance()->get('lang.ItemRepeatsLabel') ?></label>
        <select id="repeats-type" name="repeats" class="extended" data-default="never">
            <option value="never"><?php echo Base::instance()->get('lang.RepetsNever') ?></option>
            <option value="repeats-daily"><?php echo Base::instance()->get('lang.RepeatsDaily') ?></option>
            <option value="repeats-weekly"><?php echo Base::instance()->get('lang.RepeatsWeekly') ?></option>
            <option value="repeats-monthly"><?php echo Base::instance()->get('lang.RepeatsMonthly') ?></option>
            <option value="repeats-yearly"><?php echo Base::instance()->get('lang.RepeatsYearly') ?></option>
        </select>
        
        <div id="recurrence-options">
            <div id="repeats-daily-block" class="recurrence-block">
                <label for="daily-repeat-interval"><?php echo Base::instance()->get('lang.RepeatEvery') ?></label>
                <select id="daily-repeat-interval" name="dailyRepeatInterval">
<?php for ($i = 1; $i <= 30; $i++): ?>
                    <option value="<?php echo $i?>"><?php echo $i ?></option>
<?php endfor; ?>
                </select>
                <span class="label"><?php echo Base::instance()->get('lang.Days') ?></span>
                <br/>
                <span class="label"><?php echo Base::instance()->get('lang.Ends') ?></span>
                <label><input type="radio" name="endDateTriggerDaily" value="false" checked="checked" /> <?php echo Base::instance()->get('lang.Never') ?></label>
                <label>
                    <input type="radio" name="endDateTriggerDaily" value="true" />
                    <?php echo Base::instance()->get('lang.Until') ?>
                </label>
                <input type="text" name="endDateDaily" class="datepicker" disabled="disabled" autocomplete="off" />
            </div>
            
            <div id="repeats-weekly-block" class="recurrence-block">
                <label for="weeklyRepeatInterval"><?php echo Base::instance()->get('lang.RepeatEvery') ?></label>
                <select id="weeklyRepeatInterval" name="weeklyRepeatInterval">
<?php for ($i = 1; $i <= 30; $i++): ?>
                    <option value="<?php echo $i?>"><?php echo $i ?></option>
<?php endfor; ?>
                </select>
                <span class="label"><?php echo Base::instance()->get('lang.Weeks') ?></span>
                <br/>
                <span class="label"><?php echo Base::instance()->get('lang.RepeatsOn') ?></span>
                <label><input type="checkbox" name="repeatDayOfWeek[]" value="MO" /> <?php echo Base::instance()->get('lang.Mon') ?></label>
                <label><input type="checkbox" name="repeatDayOfWeek[]" value="TU" /> <?php echo Base::instance()->get('lang.Tue') ?></label>
                <label><input type="checkbox" name="repeatDayOfWeek[]" value="WE" /> <?php echo Base::instance()->get('lang.Wed') ?></label>
                <label><input type="checkbox" name="repeatDayOfWeek[]" value="TH" /> <?php echo Base::instance()->get('lang.Thu') ?></label>
                <label><input type="checkbox" name="repeatDayOfWeek[]" value="FR" /> <?php echo Base::instance()->get('lang.Fri') ?></label>
                <label><input type="checkbox" name="repeatDayOfWeek[]" value="SA" /> <?php echo Base::instance()->get('lang.Sat') ?></label>
                <label><input type="checkbox" name="repeatDayOfWeek[]" value="SU" /> <?php echo Base::instance()->get('lang.Sun') ?></label>
                <br/>
                <span class="label"><?php echo Base::instance()->get('lang.Ends') ?></span>
                <label><input type="radio" name="endDateTriggerWeekly" value="false" checked="checked" /> <?php echo Base::instance()->get('lang.Never') ?></label>
                <label>
                    <input type="radio" name="endDateTriggerWeekly" value="true" />
                    <?php echo Base::instance()->get('lang.Until') ?>
                </label>
                <input type="text" name="endDateWeekly" class="datepicker" disabled="disabled" autocomplete="off" />
            </div>
            
            <div id="repeats-monthly-block" class="recurrence-block">
                <label for="monthlyRepeatInterval"><?php echo Base::instance()->get('lang.RepeatEvery') ?></label>
                <select id="monthlyRepeatInterval" name="monthlyRepeatInterval">
<?php for ($i = 1; $i <= 30; $i++): ?>
                    <option value="<?php echo $i?>"><?php echo $i ?></option>
<?php endfor; ?>
                </select>
                <span class="label"><?php echo Base::instance()->get('lang.Months') ?></span>
                <br/>
                
                <label>
                    <input type="radio" name="monthlyRecurrenceType" value="dayOfMonth" checked="checked" />
                    <?php echo Base::instance()->get('lang.DayOfMonth') ?>
                </label>
                <select name="dayOfMonthRecurrence">
<?php for ($i = 1; $i <= 30; $i++): ?>
                    <option value="<?php echo $i?>"><?php echo $i ?></option>
<?php endfor; ?>
                    <option value="-1"><?php echo Base::instance()->get('lang.Last') ?></option>
                </select>
                <br/>
                <label>
                    <input type="radio" name="monthlyRecurrenceType" value="dayOfWeek" />
                    <?php echo Base::instance()->get('lang.DayOfWeek') ?>
                </label>
                <select name="dayOfWeekRecurrenceNumber">
<?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i?>"><?php echo $i ?></option>
<?php endfor; ?>
                    <option value="-1"><?php echo Base::instance()->get('lang.Last') ?></option>
                </select>
                <select name="dayOfWeekRecurrenceDay">
                    <option value="MO"><?php echo Base::instance()->get('lang.Monday') ?></option>
                    <option value="TU"><?php echo Base::instance()->get('lang.Tuesday') ?></option>
                    <option value="WE"><?php echo Base::instance()->get('lang.Wednesday') ?></option>
                    <option value="TH"><?php echo Base::instance()->get('lang.Thursday') ?></option>
                    <option value="FR"><?php echo Base::instance()->get('lang.Friday') ?></option>
                    <option value="SA"><?php echo Base::instance()->get('lang.Saturday') ?></option>
                    <option value="SU"><?php echo Base::instance()->get('lang.Sunday') ?></option>
                </select>
                <br/>
                
                <span class="label"><?php echo Base::instance()->get('lang.Ends') ?></span>
                <label><input type="radio" name="endDateTriggerMonthly" value="false" checked="checked" /> <?php echo Base::instance()->get('lang.Never') ?></label>
                <label>
                    <input type="radio" name="endDateTriggerMonthly" value="true" />
                    <?php echo Base::instance()->get('lang.Until') ?>
                </label>
                <input type="text" name="endDateMonthly" class="datepicker" disabled="disabled" autocomplete="off" />
            </div>
            
            <div id="repeats-yearly-block" class="recurrence-block">
                <label for="yearlyRepeatInterval"><?php echo Base::instance()->get('lang.RepeatEvery') ?></label>
                <select id="yearlyRepeatInterval" name="yearlyRepeatInterval">
<?php for ($i = 1; $i <= 30; $i++): ?>
                    <option value="<?php echo $i?>"><?php echo $i ?></option>
<?php endfor; ?>
                </select>
                <span class="label"><?php echo Base::instance()->get('lang.Years') ?></span>
                <br/>
                <span class="label"><?php echo Base::instance()->get('lang.Ends') ?></span>
                <label><input type="radio" name="endDateTriggerYearly" value="false" checked="checked" /> <?php echo Base::instance()->get('lang.Never') ?></label>
                <label>
                    <input type="radio" name="endDateTriggerYearly" value="true" />
                    <?php echo Base::instance()->get('lang.Until') ?>
                </label>
                <input type="text" name="endDateYearly" class="datepicker" disabled="disabled" autocomplete="off" />
            </div>
        </div>
    </td>
</tr>