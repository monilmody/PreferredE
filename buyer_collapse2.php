<div class="container">
                  <div class="panel-group">
                    <div class="panel panel-default">
                      
                      <div id="<?php echo $collapseID.$number.$sequence2;?>" class="panel-collapse collapse">
                      
                       <div class="table" style="width: 100%;table-layout: fixed;">
                          <div class="row">
                         	  <div class="cell" style="width: 5%;">
                         	 	<i>
                                	BUYER:
                                </i>
                              </div>
                              <div class="cell" style="width: 45%;">
                                  <i>
                                  <?php
                                  echo $row1[Purlname].' '.$row1[Purfname];
                                  ?>
                                  </i>
                              </div>
                              <div class="cell" style="width: 5%;">
                                  <i>
                                    ADDRESS:
                                  </i>
                              </div>
                              <div class="cell" style="width: 45%;">
                                  <i>
                                  <?php
                                  echo $row1[Sbcity].', '.$row1[Sbstate].', '.$row1[Sbcountry];
                                  ?>
                                  </i>
                              </div>
                          </div>
                  		</div>
                      </div>
                    </div>
                  </div>
                </div> 